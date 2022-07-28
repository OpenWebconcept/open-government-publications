<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use SudwestFryslan\OpenGovernmentPublications\Service;
use SudwestFryslan\OpenGovernmentPublications\Container;
use SudwestFryslan\OpenGovernmentPublications\Entities\Settings;
use SudwestFryslan\OpenGovernmentPublications\Entities\Publication;
use SudwestFryslan\OpenGovernmentPublications\Entities\ImportOptions;
use SudwestFryslan\OpenGovernmentPublications\Entities\ServiceRecord;
use SudwestFryslan\OpenGovernmentPublications\Services\ImportService;
use SudwestFryslan\OpenGovernmentPublications\Services\PublicationService;
use SudwestFryslan\OpenGovernmentPublications\Entities\Service as ApiService;

class ImportProvider extends ServiceProvider
{
    protected int $max_import = 10;
    protected int $limit_import = 3000;

    protected Settings $settings;
    protected ImportOptions $options;
    protected ImportService $importService;
    protected PublicationService $publicationService;

    public function __construct(
        Container $container,
        Settings $settings,
        ImportOptions $importOptions,
        ImportService $importService,
        PublicationService $publicationService
    ) {
        $this->settings = $settings;
        $this->options = $importOptions;
        $this->importService = $importService;
        $this->publicationService = $publicationService;

        parent::__construct($container);
    }

    public function register(): void
    {
        add_action('open_govpub_check_import_publications', [$this, 'check_import_publications']);
        add_action('open_govpub_task_import_publications', [$this, 'run_import_publications']);
        add_action('wp_ajax_import_open_govpub', [$this, 'import_open_govpub_data']);
    }

    public function check_import_publications()
    {
        if ($this->importService->isImportCheckLocked()) {
            return false;
        }

        $totalImport = $this->options->get('total_import', []);

        if (! empty($totalImport)) {
            return $totalImport;
        }

        $this->importService->lockImportCheck();

        $import = $this->create_current_services_import();

        if ($import && isset($import['current_import'])) {
            $this->options->save('current_import', $import['current_import']);
            $this->options->save('total_import', $import['total_import']);

            $totalImport = $import['total_import'];
        }

        $this->importService->unlockImportCheck();

        return $totalImport;
    }

    public function run_import_publications()
    {
        $totalImport = $this->options->get('total_import', []);

        if ($this->importService->isImportLocked() || empty($totalImport)) {

            /**
             * @todo better error handling / result feedback
             */
            return false;
        }

        $this->importService->lockImport();

        // Check if import completed
        if ($totalImport['status'] >= $totalImport['max_num']) {
            $this->save_services_last_dates();

            $this->options->save('last_import_date', $totalImport['import_date']);
            $this->options->delete('total_import');

            $this->importService->unlockImport();

            /**
             * @todo better error handling / result feedback
             */

            return false;
        }

        try {
            $service = $this->get_current_import_service();
            $results = $this->import_by_import_service($service);
        } catch (\Throwable $e) {
            $this->importService->unlockImport();

            error_log($e->getMessage());

            /**
             * @todo better error handling / result feedback
             */

            return false;
        }

        // If the import failed, abort.
        if (! $results || ! isset($results['imported'])) {
            $this->importService->unlockImport();

            return $totalImport;
        }

        $imported_i = $service['status'] + $results['imported'];
        $max_results = $results['max_num_records'];

        $new_status = [
            'status'            => $imported_i,
            'max_num'           => $max_results,
            'total_found'       => $results['total_found'],
            'date_offset'       => $results['date_offset'],
            'last_record_date'  => $results['last_record_date']
        ];

        $currentImport = (array) $this->options->current_import;
        $currentImport[$service['service_id']] = $new_status;
        $this->options->update('current_import', $currentImport);

        $totalImport = $this->recreate_total_import_by_current_import();

        $this->options->update('total_import', $totalImport);

        $this->importService->unlockImport();

        return $totalImport;
    }

    /**
     * @return never
     */
    public function import_open_govpub_data()
    {
        $results = ['status' => 'done'];

        // Check if not already checked for needed update
        if (! isset($_REQUEST['checked']) || $_REQUEST['checked'] == 0) {
            $total_import = $this->check_import_publications();
        } else {
            try {
                $total_import = $this->run_import_publications();
            } catch (\Exception $e) {
                $results['error'] = $e->getMessage();
            }
        }

        // Check if total import ran
        if ($total_import && isset($total_import['status'])) {
            $progress   = $total_import['status'] > 0 ? $total_import['status'] / $total_import['max_num'] : 0;
            $progress   = floor($progress * 10000) / 100;

            $importMessage = __('never', 'open-govpub');
            $lastImport = $this->options->get('last_import_date', false);
            if ($lastImport) {
                $importMessage = wp_date('j F Y H:i:s', strtotime($lastImport));
            }

            $results    = [
                'status'        => 'running',
                'progress'      => $progress,
                'details'       => $total_import,
                'import_string' => $importMessage,
            ];
        }

        $results['total_import'] = $total_import;
        $results['checked'] = ! isset($_REQUEST['checked']) || $_REQUEST['checked'] == 0;

        header('Content-Type: application/json');

        echo json_encode($results);

        exit;
    }

    protected function get_service_last_date(
        string $serviceIdentifier,
        $lastDates = false,
        $default = '1990-01-01'
    ) {
        $lastDates = $lastDates ?: $this->options->get('last_import_dates', []);

        if (is_array($lastDates) && ! empty($lastDates)) {
            return $lastDates[$serviceIdentifier] ?? $default;
        }

        return $default;
    }

    protected function save_services_last_dates(): array
    {
        $lastImportDates = (array) $this->options->get('last_import_dates', []);
        $currentImports = (array) $this->options->get('current_import', []);

        foreach ($currentImports as $serviceId => $currentImport) {
            // If the status more then teh max amount found the last date can be today
            if ($currentImport['status'] >= $currentImport['total_found']) {
                $lastImportDates[$serviceId] = date('Y-m-d');
            } else {
                // Set the date as last record date
                $lastImportDates[$serviceId] = $currentImport['last_record_date'];
            }
        }

        $this->options->save('last_import_dates', $lastImportDates);

        return $lastImportDates;
    }

    /**
     * @return array|false
     */
    protected function create_current_services_import()
    {
        if ($this->settings->isEmpty('creator')) {
            return false;
        }

        $services = $this->container->get('services.config');
        if (empty($services) || !is_array($services)) {
            return false;
        }

        $current_import = [];
        $max_items = $total_items = 0;
        $last_dates = $this->options->last_import_dates;

        foreach ($services as $identifier => $service) {
            // Get the last service import date
            $last_date = $this->get_service_last_date($identifier, $last_dates);

            $service = new Service($service);
            $service->set_limited_offset()->set_max_records(1);
            $service->set_query([
                'creator'       => $this->settings->creator,
                'created_at'    => [
                    'value'         => date_i18n('Y-m-d', strtotime($last_date)),
                    'compare'       => '>='
                ]
            ]);

            $results = $service->get_mapped_results();

            // Check if pagination items exists
            if (isset($results['pagination']['max_num_records'])) {
                $found_items = intval($results['pagination']['max_num_records']);
                $total_found = intval($results['pagination']['total_found']);

                // Check if any items found
                if ($found_items > 0) {
                    // Add the current service item
                    $current_import[$identifier] = [
                        'status'        => 0,
                        'max_num'       => $found_items,
                        'total_found'   => $total_found,
                        'date_offset'   => $last_date
                    ];

                    // Add found items count to total
                    $max_items      += $found_items;
                    $total_items    += $total_found;
                }
            }
        }

        return [
            'current_import'    => $current_import,
            'total_import'      => [
                'status'            => 0,
                'max_num'           => $max_items,
                'total_num'         => $total_items,
                'import_date'       => date_i18n('Y-m-d H:i:s')
            ]
        ];
    }

    protected function get_current_import_service()
    {
        // Get all current imports
        $current_imports = $this->options->current_import;

        // Loop trough the services to find a service that needs importing
        foreach ($current_imports as $service_id => $c_import) {
            // If status is less then the max number
            if ($c_import['status'] < $c_import['max_num']) {
                // Get services config
                // $services = get_open_govpub_service_config();
                $services = $this->container->get('services.config');

                // Return the service
                return [
                    'service_id'    => $service_id,
                    'service'       => $services[$service_id]
                ] + $c_import;
            }
        }

        return false;
    }

    protected function recreate_total_import_by_current_import()
    {
        // Get import variable
        $current_imports    = $this->options->current_import;
        $total_import       = $this->options->total_import;

        // Check if total import status isset
        if (isset($total_import['status'])) {
            // Reset values
            $total_import['status']     = 0;
            $total_import['max_num']    = 0;

            // Loop trough current imports
            foreach ($current_imports as $current_import) {
                // Check if current import has status
                if (isset($current_import['status'])) {
                    // Add numbers
                    $total_import['status']     += $current_import['status'];
                    $total_import['max_num']    += $current_import['max_num'];
                }
            }
        }

        // Return the total import
        return $total_import;
    }

    protected function savePublications($results, ApiService $apiService): int
    {
        $saved = 0;
        foreach ($results as $result) {
            $this->savePublication($result, $apiService);
            $saved++;
        }

        return $saved;
    }

    protected function savePublication(ServiceRecord $record, ApiService $apiService): Publication
    {
        if ($record->exists()) {
            $publication = $this->publicationService->update($record);
        } else {
            $publication = $this->publicationService->create($record);
        }

        $this->publicationService->saveMeta($publication, $apiService, $record);
        $this->publicationService->saveSearchmeta($publication, $record);

        return $publication;
    }

    /**
     * @return array|false
     */
    protected function import_by_import_service($import_service)
    {
        if ($this->settings->isEmpty('creator')) {
            return false;
        }

        $apiService = $import_service['service'];

        $service = new Service($apiService);
        $service->set_offset($import_service['status']);
        $service->set_max_records($this->max_import);
        $service->set_query([
            'creator'       => $this->settings->creator,
            'created_at'    => [
                'value'         => date_i18n('Y-m-d', strtotime($import_service['date_offset'])),
                'compare'       => '>='
            ]
        ]);

        $results = $service->get_mapped_results();

        if (! isset($results['data'])) {
            return false;
        }

        $last_record = $service->get_last_record();
        $countSaved = $this->savePublications($results['data'], $apiService);

        return [
            'imported'          => $countSaved,
            'max_num_records'   => $results['pagination']['max_num_records'],
            'total_found'       => $results['pagination']['total_found'],
            'date_offset'       => $import_service['date_offset'],
            'last_record_date'  => $last_record['created_at'],
        ];
    }
}
