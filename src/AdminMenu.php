<?php

namespace SudwestFryslan\OpenGovernmentPublications;

class AdminMenu implements ServiceProviderInterface
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        add_action('admin_menu', array($this, 'add_admin_pages'), 10);
    }

    public function add_admin_pages()
    {
        // Add import option to the menu
        add_submenu_page(
            'edit.php?post_type=open_govpub',
            __('Open Government Publications - import', 'open-govpub'),
            __('Import options', 'open-govpub'),
            'manage_options',
            'open-govpub',
            fn() => require $this->container->get('OPEN_GOVPUB_DIR') . '/views/admin/view-open-govpub-import.php'
        );

        // Add the settings page to the menu
        add_submenu_page(
            'edit.php?post_type=open_govpub',
            __('Open Government Publications - settings', 'open-govpub'),
            __('Settings', 'open-govpub'),
            'manage_options',
            'open-govpub-settings',
            array($this, 'show_settings_page')
        );
    }

    public function show_settings_page()
    {

        // Get the current tab
        $c_tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : false);

        if ($c_tab == 'reset') {
            // Get current rest action
            $c_reset = (isset($_GET['reset']) ? sanitize_text_field($_GET['reset']) : '');

            // Include the reset view
            require $this->container->get('OPEN_GOVPUB_DIR') . '/views/admin/view-open-govpub-reset.php';
        } else if ($c_tab == 'endpoints') {
            // Get the api args
            $search_args    = get_open_govpub_search_api_args();
            $types_args     = get_open_govpub_types_api_args();

            // require the endpoints view
            require $this->container->get('OPEN_GOVPUB_DIR') . '/views/admin/view-open-govpub-endpoints.php';
        } else {
            // require the settings view
            require $this->container->get('OPEN_GOVPUB_DIR') . '/views/admin/view-open-govpub-settings.php';
        }
    }
}
