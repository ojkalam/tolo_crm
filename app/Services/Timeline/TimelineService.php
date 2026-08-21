<?php

declare(strict_types=1);

namespace App\Services\Timeline;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class TimelineService
{
    /**
     * @return LengthAwarePaginator<mixed>
     */
    public function getTimeline(
        string $orgId,
        string $subjectType,
        string $subjectId,
        int $page = 1,
        int $perPage = 20
    ): LengthAwarePaginator {
        $resolvedClass = $this->resolveModelClass($subjectType);

        // 1. Fetch CRM Activities (Tasks, Calls, Notes, Meetings)
        $activities = Activity::where('organization_id', $orgId)
            ->where('subjectable_type', $resolvedClass)
            ->where('subjectable_id', $subjectId)
            ->with('user')
            ->get()
            ->map(function (Activity $act) {
                return [
                    'id' => $act->id,
                    'category' => 'activity',
                    'type' => $act->type?->value ?? $act->type,
                    'title' => $act->title,
                    'description' => $act->description,
                    'is_completed' => $act->completed_at !== null,
                    'due_date' => $act->due_date?->toIso8601String(),
                    'completed_at' => $act->completed_at?->toIso8601String(),
                    'metadata' => $act->metadata ?? [],
                    'user' => $act->user ? [
                        'id' => $act->user->id,
                        'name' => $act->user->name,
                        'email' => $act->user->email,
                    ] : null,
                    'timestamp' => $act->created_at?->toIso8601String() ?? now()->toIso8601String(),
                    'created_at' => $act->created_at,
                ];
            });

        // 2. Fetch Spatie Model Audit Events
        $auditLogs = SpatieActivity::where('subject_type', $resolvedClass)
            ->where('subject_id', $subjectId)
            ->with('causer')
            ->get()
            ->map(function (SpatieActivity $log) {
                /** @var \App\Models\User|null $causer */
                $causer = $log->causer;

                return [
                    'id' => (string) $log->id,
                    'category' => 'audit',
                    'type' => 'model_' . ($log->event ?? 'updated'),
                    'title' => ucfirst($log->event ?? 'updated') . ' ' . class_basename((string) $log->subject_type),
                    'description' => $log->description,
                    'is_completed' => true,
                    'due_date' => null,
                    'completed_at' => $log->created_at?->toIso8601String(),
                    'metadata' => [
                        'changes' => $log->properties['attributes'] ?? $log->properties ?? [],
                        'old' => $log->properties['old'] ?? [],
                    ],
                    'user' => $causer ? [
                        'id' => $causer->id,
                        'name' => $causer->name,
                        'email' => $causer->email,
                    ] : null,
                    'timestamp' => $log->created_at?->toIso8601String() ?? now()->toIso8601String(),
                    'created_at' => $log->created_at,
                ];
            });

        // 3. Merge and Sort Chronologically Descending
        /** @var Collection<int, array<string, mixed>> $merged */
        $merged = $activities->concat($auditLogs)->sortByDesc(fn ($item) => $item['created_at'])->values();

        // 4. Paginate
        $total = $merged->count();
        $slice = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function resolveModelClass(string $type): string
    {
        return match (strtolower($type)) {
            'company', 'companies', 'app\models\company' => Company::class,
            'contact', 'contacts', 'app\models\contact' => Contact::class,
            'lead', 'leads', 'app\models\lead' => Lead::class,
            'deal', 'deals', 'app\models\deal' => Deal::class,
            default => $type,
        };
    }
}
