<?php

namespace SudwestFryslan\OpenGovernmentPublications;

class PostType implements ServiceProviderInterface
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        add_action('init', [$this, 'registerPostType'], 10);
        add_action('init', [$this, 'registerTaxonomy'], 10);
    }

    public function registerPostType()
    {
        $capabilities = ['create_posts' => 'do_not_allow'];
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $capabilities = [];
        }

        $labels = [
            'name'                => __('Open Government Publications', 'open-govpub'),
            'singular_name'       => __('Open Government Publications', 'open-govpub'),
            'menu_name'           => __('Open Publications', 'open-govpub'),
            'parent_item_colon'   => __('Parent Publications', 'open-govpub'),
            'all_items'           => __('All Publications', 'open-govpub'),
            'view_item'           => __('View Open Government Publication', 'open-govpub'),
            'add_new_item'        => __('Add New Publication', 'open-govpub'),
            'add_new'             => __('Add New', 'open-govpub'),
            'edit_item'           => __('Edit Publication', 'open-govpub'),
            'update_item'         => __('Update Publication', 'open-govpub'),
            'search_items'        => __('Search Open Government Publication', 'open-govpub'),
            'not_found'           => __('Not Found', 'open-govpub'),
            'not_found_in_trash'  => __('Not found in Trash', 'open-govpub'),
        ];

        $args = [
            'label'               => __('Open Government Publications', 'open-govpub'),
            'description'         => __('Open Government Publications', 'open-govpub'),
            'labels'              => $labels,
            'supports'            => [
                'title'
            ],
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'show_in_admin_bar'     => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-text-page',
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
            'capabilities'          => $capabilities,
        ];

        // Registering Custom Post Type
        register_post_type('open_govpub', $args);
    }

    public function registerTaxonomy()
    {
        $labels = [
            'name'              => __('Type', 'open-govpub'),
            'singular_name'     => __('Type', 'open-govpub'),
            'search_items'      => __('Search Types', 'open-govpub'),
            'all_items'         => __('All Types', 'open-govpub'),
            'parent_item'       => __('Parent Type', 'open-govpub'),
            'parent_item_colon' => __('Parent Type:', 'open-govpub'),
            'edit_item'         => __('Edit Type', 'open-govpub'),
            'update_item'       => __('Update Type', 'open-govpub'),
            'add_new_item'      => __('Add New Type', 'open-govpub'),
            'new_item_name'     => __('New Type Name', 'open-govpub'),
            'menu_name'         => __('Types', 'open-govpub'),
        ];

        register_taxonomy('open_govpub_type', 'open_govpub', [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true
        ]);
    }
}
