<?php

namespace SudwestFryslan\OpenGovernmentPublications;

class Init implements ServiceProviderInterface
{
    protected Container $container;
    protected AssetLoader $loader;

    public function __construct(Container $container, AssetLoader $loader)
    {
        $this->container = $container;
        $this->assetLoader = $loader;
    }

    public function register()
    {
        // Import organizations on activation and daily by cronjob
        add_action('open_govpub_import_organization', array($this, 'importOrganizations'));

        // Enqueue admin styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
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
            array(),
            $this->container->get('plugin.version')
        );

        wp_enqueue_script(
            'open_govpub',
            $this->assetLoader->getUrl('js/admin.js'),
            array('jquery'),
            $this->container->get('plugin.version'),
            true
        );

        wp_localize_script('open_govpub', 'open_govpub', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }
}
