<?php

namespace SudwestFryslan\OpenGovernmentPublications\Commands;

use WP_CLI;
use Throwable;
use SudwestFryslan\OpenGovernmentPublications\Providers\ServiceProvider;

use function WP_CLI\Utils\format_items as WP_CLI_format_items;

class Check
{
    protected string $command = 'govpub check';
    protected ServiceProvider $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }

    // wp govpub check
    public function __invoke()
    {
        return $this->check();
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    protected function check()
    {
        if (! method_exists($this->provider, 'check_import_publications')) {
            return WP_CLI::error(sprintf(
                'The given ServiceProvider "%s" has no method "check_import_publications".',
                $this->provider::class
            ));
        }

        try {
            $result = $this->provider->check_import_publications();
        } catch (Throwable $e) {
            return WP_CLI::error(sprintf(
                'Import check failed, caught error: "%s".',
                $e->getMessage()
            ));
        }

        if (! $result) {
            return WP_CLI::error('Import check halted.');
        }

        WP_CLI_format_items('table', [$result], array_keys($result));
        WP_CLI::success('Import check OK.');

        return WP_CLI::halt(0);
    }
}
