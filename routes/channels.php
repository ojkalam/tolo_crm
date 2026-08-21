<?php

declare(strict_types=1);

use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Private Organization Channel (Tenant-wide alerts)
Broadcast::channel('organization.{orgId}', function (User $user, string $orgId) {
    return (string) $user->organization_id === (string) $orgId;
});

// Private User Channel (Personal tasks & notifications)
Broadcast::channel('user.{userId}', function (User $user, string $userId) {
    return (string) $user->id === (string) $userId;
});

// Presence Pipeline Channel (Real-time collaborative Kanban updates)
Broadcast::channel('pipeline.{pipelineId}', function (User $user, string $pipelineId) {
    $pipeline = Pipeline::where('id', $pipelineId)
        ->where('organization_id', $user->organization_id)
        ->first();

    if (! $pipeline) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'avatar_url' => $user->avatar_url,
    ];
});
