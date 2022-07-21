<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use SudwestFryslan\OpenGovernmentPublications\Providers\ServiceProvider;

class Init extends ServiceProvider
{
    protected AssetLoader $loader;

    public function __construct(Container $container, AssetLoader $loader)
    {
        $this->assetLoader = $loader;

        parent::__construct($container);
    }

    public function register()
    {
        // Import organizations on activation and daily by cronjob
        add_action('open_govpub_import_organization', [$this, 'importOrganizations']);

        // Enqueue admin styles and scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function importOrganizations()
    {

        // Get the organizations from source
        $list = get_open_govpub_source_organizations();

        // Check if list is not empty
        if (!empty($list)) {
            // Save the list
            update_option('open_govpub_organization', $list);
        }

        return true;
    }

    public function enqueueScripts()
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
}
