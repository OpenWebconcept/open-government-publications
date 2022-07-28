<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use SudwestFryslan\OpenGovernmentPublications\Container;
use SudwestFryslan\OpenGovernmentPublications\Services\EventService;
use SudwestFryslan\OpenGovernmentPublications\Entities\ImportOptions;
use SudwestFryslan\OpenGovernmentPublications\Views\Reset as ResetView;
use SudwestFryslan\OpenGovernmentPublications\Views\Import as ImportView;
use SudwestFryslan\OpenGovernmentPublications\Views\Settings as SettingsView;
use SudwestFryslan\OpenGovernmentPublications\Views\Endpoints as EndpointsView;

class AdminMenuProvider extends ServiceProvider
{
    protected EventService $events;

    public function __construct(Container $container, EventService $eventService)
    {
        $this->events = $eventService;

        parent::__construct($container);
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addPages'], 10);
    }

    public function addPages(): void
    {
        add_submenu_page(
            'edit.php?post_type=open_govpub',
            __('Open Government Publications - import', 'open-govpub'),
            __('Import options', 'open-govpub'),
            'manage_options',
            'open-govpub',
            [$this, 'getImportPage']
        );

        add_submenu_page(
            'edit.php?post_type=open_govpub',
            __('Open Government Publications - settings', 'open-govpub'),
            __('Settings', 'open-govpub'),
            'manage_options',
            'open-govpub-settings',
            [$this, 'getSettingsPage']
        );
    }

    public function getImportPage()
    {
        $importOptions = $this->container->get(ImportOptions::class);

        $lastImport = $importOptions->last_import_date;

        return $this->container->get(ImportView::class)->output([
            'check_import_schedule'     => $this->events->getFormattedSchedule(
                'open_govpub_check_import_publications',
                'd-m-Y H:i'
            ),
            'task_import_schedule'      => $this->events->getFormattedSchedule(
                'open_govpub_task_import_publications',
                'd-m-Y H:i'
            ),

            /**
             * @todo move to separate class
             */
            'lastImport'                => $lastImport ? strtotime($lastImport) : null,
            'totalImport'               => $importOptions->total_import,
        ]);
    }

    public function getSettingsPage()
    {
        /**
         * @todo move to separate Request class
         */
        $currentTab = sanitize_text_field($_GET['tab'] ?? '');

        if ($currentTab == 'reset') {
            return $this->container->get(ResetView::class)->output([
                'c_reset' => sanitize_text_field(($_GET['reset'] ?? ''))
            ]);
        }

        if ($currentTab == 'endpoints') {
            return $this->container->get(EndpointsView::class)->output([
                'searchArguments'   => $this->container->get('search.api.args'),
                'typesArguments'    => $this->container->get('types.api.args'),
            ]);
        }

        return $this->container->get(SettingsView::class)->output();
    }
}
