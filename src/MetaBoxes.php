<?php

namespace SudwestFryslan\OpenGovernmentPublications;

class MetaBoxes implements ServiceProviderInterface
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        // Register the meta boxes
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
    }

    public function register_meta_boxes()
    {

        // Register the meta box
        add_meta_box(
            'opengovpub-meta-box',
            __('Metadata'),
            array($this, 'render_meta_box'),
            'open_govpub',
            'normal',
            'default'
        );

        // Register the search meta box
        add_meta_box(
            'opengovpub-search-meta-box',
            __('Search string'),
            array($this, 'render_search_meta_box'),
            'open_govpub',
            'normal',
            'default'
        );
    }

    public function render_meta_box($post)
    {

        // Set the variable
        $identifier = get_post_meta($post->ID, 'open_govpub_identifier', true);
        $permalink  = get_post_meta($post->ID, 'open_govpub_permalink', true);
        $meta       = get_post_meta($post->ID, 'open_govpub_meta', true);

        // Include the view
        require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-meta-box.php';
    }

    public function render_search_meta_box($post)
    {

        // Get the search meta
        $search_meta = get_post_meta($post->ID, 'search_meta', true);

        // Include the view
        require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-meta-box-search.php';
    }
}
