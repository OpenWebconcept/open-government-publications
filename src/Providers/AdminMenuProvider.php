<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

class AdminMenuProvider extends ServiceProvider
{
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
            fn() => require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-import.php'
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

    public function getSettingsPage(): void
    {
        $currentTab = sanitize_text_field($_GET['tab'] ?? '');

        if ($currentTab == 'reset') {
            // Get current rest action
            $c_reset = (isset($_GET['reset']) ? sanitize_text_field($_GET['reset']) : '');

            require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-reset.php';
        } elseif ($currentTab == 'endpoints') {
            // Get the api args
            $search_args = get_open_govpub_search_api_args();
            $types_args = get_open_govpub_types_api_args();

            require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-endpoints.php';
        } else {
            require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-settings.php';
        }
    }
}
