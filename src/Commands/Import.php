<?php

namespace SudwestFryslan\OpenGovernmentPublications\Commands;

use WP_CLI;
use Throwable;
use SudwestFryslan\OpenGovernmentPublications\Providers\ServiceProvider;

use function WP_CLI\Utils\format_items as WP_CLI_format_items;

class Import
{
    protected string $command = 'govpub import';
    protected ServiceProvider $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }

    // wp govpub import
    public function __invoke()
    {
        return $this->import();
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    protected function import()
    {
        if (! method_exists($this->provider, 'run_import_publications')) {
            return WP_CLI::error(sprintf(
                'The given ServiceProvider "%s" has no method "run_import_publications".',
                $this->provider::class
            ));
        }

        try {
            $result = $this->provider->run_import_publications();
        } catch (Throwable $e) {
            return WP_CLI::error(sprintf(
                'Import failed, caught error: "%s".',
                $e->getMessage()
            ));
        }

        if (! $result) {
            return WP_CLI::error('Import halted.');
        }

        WP_CLI_format_items('table', [$result], array_keys($result));
        WP_CLI::success('Import OK.');

        return WP_CLI::halt(0);
    }
}
