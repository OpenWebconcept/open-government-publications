<?php

namespace SudwestFryslan\OpenGovernmentPublications\Services;

use Throwable;
use DateTime;

class EventService
{
    public function schedule(): void
    {
        // Add the import organization schedule
        if (! wp_next_scheduled('open_govpub_import_organization')) {
            wp_schedule_event(time(), 'daily', 'open_govpub_import_organization');
        }

        // Add the publications schedule thats queues the import
        if (! wp_next_scheduled('open_govpub_check_import_publications')) {
            wp_schedule_event(time(), 'daily', 'open_govpub_check_import_publications');
        }

        // Add the import publications schedule
        if (! wp_next_scheduled('open_govpub_task_import_publications')) {
            wp_schedule_event(time(), 'hourly', 'open_govpub_task_import_publications');
        }
    }

    public function getSchedule(string $eventName): ?DateTime
    {
        try {
            $datetime = DateTime::createFromFormat('U', wp_next_scheduled($eventName));

            return $datetime ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public function getFormattedSchedule(string $eventName, string $format): string
    {
        return wp_date($format, wp_next_scheduled($eventName));
    }
}
