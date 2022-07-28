<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications;

use SudwestFryslan\OpenGovernmentPublications\Providers\ServiceProvider;

class Init extends ServiceProvider
{
    protected AssetLoader $assetLoader;

    public function __construct(Container $container, AssetLoader $loader)
    {
        $this->assetLoader = $loader;

        parent::__construct($container);
    }

    public function register(): void
    {
        // Import organizations on activation and daily by cronjob
        add_action('open_govpub_import_organization', [$this, 'importOrganizations']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * @return true
     */
    public function importOrganizations(): bool
    {
        $list = $this->getOrganisationsList();

        if (! empty($list)) {
            update_option('open_govpub_organization', $list);
        }

        return true;
    }

    public function enqueueScripts(): void
    {
        wp_enqueue_style(
            'open_govpub',
            $this->assetLoader->getUrl('css/admin.css'),
            [],
            $this->container->get('plugin.version')
        );

        wp_enqueue_script(
            'open_govpub',
            $this->assetLoader->getUrl('js/admin.js'),
            ['jquery'],
            $this->container->get('plugin.version'),
            true
        );

        wp_localize_script('open_govpub', 'open_govpub', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }

    /**
     * @todo move to separate class
     * @return array
     */
    protected function getOrganisationsList(): array
    {
        $source_url = 'https://standaarden.overheid.nl/owms/terms/Overheidsorganisatie.xml';
        $result = [];

        $xml = simplexml_load_file($source_url);

        if (isset($xml->value) && ! empty($xml->value)) {
            foreach ($xml->value as $value) {
                $result[] = $value->prefLabel->__toString();
            }
        }

        return $result;
    }
}
