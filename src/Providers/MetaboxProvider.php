<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use SudwestFryslan\OpenGovernmentPublications\Entities\Publication;

class MetaboxProvider extends ServiceProvider
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
    }

    public function register_meta_boxes()
    {
        add_meta_box(
            'opengovpub-meta-box',
            __('Metadata'),
            [$this, 'render_meta_box'],
            'open_govpub',
            'normal',
            'default'
        );

        add_meta_box(
            'opengovpub-search-meta-box',
            __('Search string'),
            [$this, 'render_search_meta_box'],
            'open_govpub',
            'normal',
            'default'
        );
    }

    public function render_meta_box($post)
    {
        $publication = new Publication($post);

        require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-meta-box.php';
    }

    public function render_search_meta_box($post)
    {
        $search_meta = get_post_meta($post->ID, 'search_meta', true);

        require $this->container->get('plugin.path') . '/views/admin/view-open-govpub-meta-box-search.php';
    }
}
