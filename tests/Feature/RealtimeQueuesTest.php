<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ActivityType;
use App\Events\LeadAssignedEvent;
use App\Events\TaskDueReminderEvent;
use App\Jobs\SendLeadAssignmentNotificationJob;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RealtimeQueuesTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->user->assignRole('SalesManager');
    }

    public function test_lead_assignment_job_can_be_queued_and_executed(): void
    {
        Queue::fake();

        $lead = Lead::factory()->create([
            'organization_id' => $this->org->id,
            'assigned_user_id' => $this->user->id,
        ]);

        SendLeadAssignmentNotificationJob::dispatch($lead, $this->user);

        Queue::assertPushed(SendLeadAssignmentNotificationJob::class, function ($job) use ($lead) {
            return $job->lead->id === $lead->id && $job->assignedUser->id === $this->user->id;
        });
    }

    public function test_lead_assigned_and_task_reminder_events_dispatch_broadcasts(): void
    {
        Event::fake([LeadAssignedEvent::class, TaskDueReminderEvent::class]);

        $lead = Lead::factory()->create(['organization_id' => $this->org->id]);
        event(new LeadAssignedEvent($lead, $this->user));

        Event::assertDispatched(LeadAssignedEvent::class, function ($event) use ($lead) {
            return $event->lead->id === $lead->id && $event->assignedUser->id === $this->user->id;
        });

        $contact = Contact::factory()->create(['organization_id' => $this->org->id]);
        $activity = Activity::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'subjectable_type' => Contact::class,
            'subjectable_id' => $contact->id,
            'type' => ActivityType::TASK,
        ]);

        event(new TaskDueReminderEvent($activity, $this->user));

        Event::assertDispatched(TaskDueReminderEvent::class, function ($event) use ($activity) {
            return $event->activity->id === $activity->id && $event->user->id === $this->user->id;
        });
    }
}
