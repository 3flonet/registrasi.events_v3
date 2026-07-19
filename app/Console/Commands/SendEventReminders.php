<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\EventEmailTemplate;
use App\Models\PendingEventBroadcast;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically send event reminders 2 days before the start date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Event Reminders Scheduler (H-2)...');

        // Find events starting in 2 days that haven't sent reminders yet
        $targetDate = Carbon::now()->addDays(2)->format('Y-m-d');
        
        $events = Event::whereDate('start_date', $targetDate)
            ->whereNull('reminder_sent_at')
            ->where('is_active', true)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events found for H-2 reminders today.');
            return;
        }

        foreach ($events as $event) {
            $this->info("Processing Event: {$event->name}");

            // 1. Determine Template
            $template = null;
            
            // Priority 1: Explicitly set reminder_template_id
            if ($event->reminder_template_id) {
                $template = EventEmailTemplate::find($event->reminder_template_id);
            }

            // Priority 2: Event-specific template with category 'reminder'
            if (!$template) {
                $template = EventEmailTemplate::where('event_id', $event->id)
                    ->where('category', 'reminder')
                    ->first();
            }

            // Priority 3: Global template with category 'reminder'
            if (!$template) {
                $template = EventEmailTemplate::whereNull('event_id')
                    ->where('category', 'reminder')
                    ->first();
            }

            if (!$template) {
                $this->warn("No reminder template found for Event: {$event->name}. Skipping.");
                continue;
            }

            // 2. Count Recipients
            $totalRecipients = $event->registrations()->count();

            if ($totalRecipients === 0) {
                $this->info("No registrants for Event: {$event->name}. Skipping.");
                $event->update(['reminder_sent_at' => now()]); // Mark as processed anyway
                continue;
            }

            // 3. Initiate Broadcast (Multi-channel)
            PendingEventBroadcast::create([
                'event_id' => $event->id,
                'template_id' => $template->id,
                'status' => 'pending',
                'type' => 'both', // Send via Email and WhatsApp
                'total_recipients' => $totalRecipients,
            ]);

            // 4. Mark as Sent
            $event->update(['reminder_sent_at' => now()]);
            $this->info("Broadcast queued for {$totalRecipients} recipients for Event: {$event->name}");
        }

        $this->info('Scheduler completed successfully.');
    }
}
