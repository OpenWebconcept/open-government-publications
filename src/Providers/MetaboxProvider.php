<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use WP_Post;
use SudwestFryslan\OpenGovernmentPublications\Entities\Publication;
use SudwestFryslan\OpenGovernmentPublications\Views\MetaBox as MetaBoxView;
use SudwestFryslan\OpenGovernmentPublications\Views\MetaBoxSearch as MetaBoxSearchView;

class MetaboxProvider extends ServiceProvider
{
    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
    }

    public function register_meta_boxes(): void
    {
        add_meta_box(
            'opengovpub-meta-box',
            __('Metadata'),
            [$this, 'renderMetaBox'],
            'open_govpub',
            'normal',
            'default'
        );

        add_meta_box(
            'opengovpub-search-meta-box',
            __('Search string'),
            [$this, 'renderSearchMetaBox'],
            'open_govpub',
            'normal',
            'default'
        );
    }

    public function renderMetaBox(WP_Post $post)
    {
        $publication = new Publication($post);

        return $this->container->get(MetaBoxView::class)->output(compact('publication'));
    }

    public function renderSearchMetaBox(WP_Post $post)
    {
        $meta = get_post_meta($post->ID, 'search_meta', true);

        return $this->container->get(MetaBoxSearchView::class)->output(compact('meta'));
    }
}
