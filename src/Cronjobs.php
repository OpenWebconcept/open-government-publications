<?php

namespace SudwestFryslan\OpenGovernmentPublications;

class Cronjobs
{
    public function schedule()
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
}
