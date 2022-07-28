<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use WP_Query;
use SudwestFryslan\OpenGovernmentPublications\Container;
use SudwestFryslan\OpenGovernmentPublications\Entities\ImportOptions;
use SudwestFryslan\OpenGovernmentPublications\Entities\Settings;

class SettingsProvider extends ServiceProvider
{
    protected Settings $settings;
    protected ImportOptions $options;

    public function __construct(Container $container, Settings $settings, ImportOptions $options)
    {
        $this->settings = $settings;
        $this->options = $options;

        parent::__construct($container);
    }

    public function register(): void
    {
        add_action('admin_init', [$this, 'add_settings']);
        add_action('admin_notices', [$this, 'add_reset_notice']);
        add_action('wp_ajax_reset_open_govpub', [$this, 'reset_open_govpub_data']);
    }

    public function add_settings(): void
    {
        register_setting('open_govpub', 'open_govpub_settings');

        add_settings_section(
            'open_govpub_settings_section',
            __('Algemeen', 'open-govpub'),
            fn() => '',
            'open_govpub'
        );

        add_settings_field(
            'creator',
            __('Publicerende organisatie', 'open-govpub'),
            [$this, 'creator_field_render'],
            'open_govpub',
            'open_govpub_settings_section'
        );
    }

    public function creator_field_render(): void
    {
        $organizations = get_option('open_govpub_organization');
        $currentCreator = $this->settings->get('creator', '');

        require $this->container->get('plugin.path') . '/views/input/creator-select.php';
    }

    public function add_reset_notice(): bool
    {
        /**
         * @todo add nonce validation
         */
        if (! isset($_GET['page']) || ! isset($_GET['tab']) || ! isset($_GET['deleted_i'])) {
            return false;
        }

        $deleted  = (int) ($_GET['deleted_i'] ?? 0);
        $maxItems  = (int) ($_GET['max_items'] ?? 0);

        if ($deleted >= $maxItems) {
            return (bool) printf(
                '<div class="notice notice-success"><p>%s</p></div>',
                __('All posts deleted', 'open-govpub')
            );
        }

        $message = sprintf(
            __('Too many posts found, %1$s of %2$s posts deleted. Please re-run the reset to delete the next %1$s posts', 'open-govpub'),
            $deleted,
            $maxItems
        );

        return (bool) printf('<div class="notice notice-warning"><p>%s</p></div>', $message);
    }

    /**
     * @return never
     */
    public function reset_open_govpub_data()
    {
        if (! isset($_POST['reset']) || empty($_POST['reset'])) {
            wp_die("Invalid action.");
        }

        $deleted = false;
        $reset = sanitize_text_field($_POST['reset']);

        switch ($reset) {
            case 'statistics':
                $this->options->reset();
                break;
            case 'posts':
                $this->options->reset();
                $deleted = $this->deletePublications();
                break;
            case 'all':
                $this->options->reset();
                $this->settings->reset();
                $deleted = $this->deletePublications();
                break;
            default:
                break;
        }

        $referer = sanitize_text_field(($_POST['referer'] ?? ''));
        $referer = add_query_arg('reset', $reset, $referer);

        if ($deleted) {
            $referer = add_query_arg($deleted, $referer);
        }

        wp_redirect($referer);
        exit();
    }

    /**
     * @psalm-return array{deleted_i: int, max_items: int}
     */
    protected function deletePublications(): array
    {
        /**
         * @todo move to separate repository
         */
        $publications = new WP_Query([
            'post_type'         => 'open_govpub',
            'posts_per_page'    => 250
        ]);

        $foundItems = $publications->found_posts;
        $deleted = 0;

        if ($publications->have_posts()) {
            while ($publications->have_posts()) {
                $publications->the_post();
                $deleted += (bool) wp_delete_post(get_the_ID(), true);
            }
        }

        wp_reset_postdata();

        return ['deleted_i' => $deleted, 'max_items' => $foundItems];
    }
}
