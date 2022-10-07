<?php

namespace SudwestFryslan\OpenGovernmentPublications\Services;

use Throwable;
use DateTime;

class EventService
{
    protected array $events  = [
        'open_govpub_import_organization'           => 'daily',
        'open_govpub_check_import_publications'     => 'daily',
        'open_govpub_task_import_publications'      => 'hourly',
    ];

    public function schedule(): void
    {
        foreach ($this->events as $event => $interval) {
            if (! wp_next_scheduled($event)) {
                wp_schedule_event(time(), $interval, $event);
            }
        }
    }

    public function unschedule(): void
    {
        foreach (array_keys($this->events) as $event) {
            wp_clear_scheduled_hook($event);
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

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getFormattedSchedule(string $eventName, string $format): string
    {
        return wp_date($format, wp_next_scheduled($eventName));
    }
}
