<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageTemplateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'transactional',
                'name' => 'E-Ticket Confirmation',
                'icon' => 'fa-envelope-open-text',
                'color' => 'indigo',
                'description' => 'Sent after registration or payment success',
                'is_manual_sendable' => true,
                'is_system' => true,
            ],
            [
                'slug' => 'auto_checkin',
                'name' => 'Check-In Notification',
                'icon' => 'fa-bolt',
                'color' => 'emerald',
                'description' => 'WhatsApp auto-sent on check-in',
                'is_manual_sendable' => false,
                'is_system' => true,
            ],
            [
                'slug' => 'event_invoice',
                'name' => 'Invoice Notification',
                'icon' => 'fa-receipt',
                'color' => 'violet',
                'description' => 'Billing instructions after paid registration',
                'is_manual_sendable' => false,
                'is_system' => true,
            ],
            [
                'slug' => 'reminder',
                'name' => 'Event Reminder',
                'icon' => 'fa-clock',
                'color' => 'amber',
                'description' => 'Manual or automated event reminders',
                'is_manual_sendable' => true,
                'is_system' => true,
            ],
            [
                'slug' => 'certificate',
                'name' => 'Certificate Notification',
                'icon' => 'fa-award',
                'color' => 'teal',
                'description' => 'E-Certificate distribution template',
                'is_manual_sendable' => true,
                'is_system' => true,
            ],
            [
                'slug' => 'event_cancellation',
                'name' => 'Event Cancellation',
                'icon' => 'fa-ban',
                'color' => 'rose',
                'description' => 'Notify participants about event cancellation',
                'is_manual_sendable' => true,
                'is_system' => true,
            ],
            [
                'slug' => 'event_feedback',
                'name' => 'Event Feedback',
                'icon' => 'fa-comment-dots',
                'color' => 'indigo',
                'description' => 'Surveys and feedback requests after event',
                'is_manual_sendable' => true,
                'is_system' => true,
            ],
            [
                'slug' => 'others',
                'name' => 'Other Templates',
                'icon' => 'fa-folder-open',
                'color' => 'slate',
                'description' => 'Custom templates for miscellaneous communication',
                'is_manual_sendable' => true,
                'is_system' => true,
            ],
        ];

        foreach ($categories as $cat) {
            \App\Models\MessageTemplateCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
