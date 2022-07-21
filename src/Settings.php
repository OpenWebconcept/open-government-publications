<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use WP_Query;

class Settings implements ServiceProviderInterface
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function register()
    {
        add_action('admin_init', [$this, 'add_settings']);
        add_action('admin_notices', [$this, 'add_reset_notice']);
        add_action('wp_ajax_reset_open_govpub', [$this, 'reset_open_govpub_data']);
    }

    public function add_settings()
    {
        // Register the setting
        register_setting('open_govpub', 'open_govpub_settings');

        // Add section
        add_settings_section(
            'open_govpub_settings_section',
            __('Algemeen', 'open-govpub'),
            [$this, 'section_intro'],
            'open_govpub'
        );

        // creator field
        add_settings_field(
            'creator',
            __('Publicerende organisatie', 'open-govpub'),
            [$this, 'creator_field_render'],
            'open_govpub',
            'open_govpub_settings_section'
        );
    }

    public function section_intro()
    {
    }

    public function creator_field_render()
    {
        // Get available creators
        $organizations = get_option('open_govpub_organization');
        $options = (is_array($organizations) ? $organizations : []);

        // Set the values as keys
        $options = array_combine($options, $options);

        // Set the name and value
        $name = 'open_govpub_settings[creator]';
        $value = get_open_govpub_setting('creator');

        // Set a description
        $desc = __(
            'Warning: please reset all publications after changing this field if a import has taken place',
            'open-govpub'
        );

        // Include the select input
        require $this->container->get('plugin.path') . '/views/input/view-open-govpud-select.php';
    }

    public function add_reset_notice()
    {

        // Set notice to false
        $notice = false;
        $type   = 'success';

        // Check if reset notice needs to be shown
        if (isset($_GET['page']) && isset($_GET['tab']) && isset($_GET['deleted_i'])) {
            // Set variable
            $deleted_i  = intval($_GET['deleted_i']);
            $max_items  = intval($_GET['max_items']);

            // Check if all items are deleted
            if ($deleted_i >= $max_items) {
                // Set notice
                $notice = __('All posts deleted', 'open-govpub');
            } else {
                // Set notice
                $type   = 'warning';
                $notice = sprintf(
                    __('To many posts found, %s of %s posts deleted. Please re-run the reset to delete the next %s posts', 'open-govpub'),
                    $deleted_i,
                    $max_items,
                    $deleted_i
                );
            }
        }

        // If notice then show
        if ($notice) {
            echo '<div class="notice notice-' . $type . '">';
            echo '<p>' . $notice . '</p>';
            echo '</div>';
        }
    }

    public function reset_open_govpub_data()
    {

        if (isset($_POST['referer'])) {
            $referer = sanitize_text_field($_POST['referer']);
        }

        // Check if reset action isset
        if (isset($_POST['reset']) && !empty($_POST['reset'])) {
            // Set default deleted posts as false
            $deleted = false;

            // Get reset action
            $reset = sanitize_text_field($_POST['reset']);

            // Switch actions
            switch ($reset) {
                case 'statistics':
                    $this->reset_statistics();
                    break;
                case 'posts':
                    $this->reset_statistics();
                    $deleted = $this->reset_posts();
                    break;
                case 'all':
                    $this->reset_statistics();
                    $this->reset_settings();
                    $deleted = $this->reset_posts();
                    break;
                default:
                    break;
            }

        // Add the action to the referer
            $referer = add_query_arg('reset', $reset, $referer);

            // Check if deleted isset
            if ($deleted) {
                // Add deleted as query arg
                $referer = add_query_arg($deleted, $referer);
            }

            // Redirect to referer
            wp_redirect($referer);
        }

        exit;
    }

    public function reset_statistics()
    {

        // Delete the option
        delete_option('open_govpub_options');
    }

    public function reset_settings()
    {

        // Delete the setting
        delete_option('open_govpub_settings');
    }

    public function reset_posts()
    {

        // Set args
        $args = [
            'post_type'         => 'open_govpub',
            'posts_per_page'    => 100
        ];

        // Set delete iteration
        $deleted_i = 0;

        // Get posts
        $the_query = new WP_Query($args);

        // Check if post found and set that post id
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();

                // Set the post id
                $post_id = get_the_ID();

                // Force delete the post
                wp_delete_post($post_id, true);

                // Add 1 to delete iteration
                $deleted_i++;
            }
        }

        // Set max items
        $max_items = $the_query->found_posts;

        // Reset the postdata
        wp_reset_postdata();

        return [
            'deleted_i' => $deleted_i,
            'max_items' => $max_items
        ];
    }
}
