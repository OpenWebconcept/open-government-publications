<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use SudwestFryslan\OpenGovernmentPublications\SearchQueryBuilder;
use SudwestFryslan\OpenGovernmentPublications\Entities\Publication;

class RestRouteProvider extends ServiceProvider
{
    protected string $namespace = 'owc/govpub/v1';

    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRestRoutes'], 10);
    }

    public function registerRestRoutes(): void
    {
        register_rest_route($this->namespace, '/types', [
            'methods'   => 'GET',
            'callback'  => [$this, 'getTypes'],
            'args'      => $this->container->get('types.api.args')
        ]);

        register_rest_route($this->namespace, '/search', [
            'methods'   => 'GET',
            'callback'  => [$this, 'search'],
            'args'      => $this->container->get('search.api.args')
        ]);
    }

    public function getTypes(WP_REST_Request $request): WP_REST_Response
    {
        $types = get_terms([
            'taxonomy'      => 'open_govpub_type',
            'hide_empty'    => (bool) $request->get_param('hide_empty')
        ]);

        if (is_wp_error($types) || empty($types)) {
            return new WP_REST_Response([], 200);
        }

        $returnType = $request->get_param('return');

        // Return the full WP_Term instances
        if ($returnType === 'object' || $returnType === 'array') {
            return new WP_REST_Response($types, 200);
        }

        $list = [];
        foreach ($types as $type) {
            $list[$type->slug] = $type->name;
        }

        return new WP_REST_Response($list, 200);
    }

    public function search(WP_REST_Request $request): WP_REST_Response
    {
        $queryBuilder = SearchQueryBuilder::fromRestRequest($request);
        $query = new WP_Query($queryBuilder->getArguments());

        $results = [
            'pagination' => [
                'found_posts'       => (int) $query->found_posts,
                'posts_per_page'    => (int) $query->query['posts_per_page'],
                'paged'             => (int) $query->query['paged'],
                'max_num_pages'     => (int) $query->max_num_pages
            ],
        ];

        if (! $query->have_posts()) {
            wp_reset_postdata();

            return new WP_REST_Response($results, 200);
        }

        $posts = array_map(function ($item) {
            return (new Publication($item))->toArray();
        }, $query->posts);

        $results['data'] = $posts;

        return new WP_REST_Response($results, 200);
    }
}
