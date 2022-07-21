<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use WP_Query;
use WP_REST_Response;

class Api implements ServiceProviderInterface
{
    protected Container $container;

    /**
     * The endpoint of the base API.
     * @var string $namespace
     */
    private $namespace = 'owc/govpub/v1';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        // Register the rest routes
        add_action('rest_api_init', array($this, 'register_rest_routes'), 10);

        // Return the meta data
        add_filter('govpub_field_identifier_value', array($this, 'get_open_govpub_identifier'), 5, 2);
        add_filter('govpub_field_permalink_value', array($this, 'get_open_govpub_permalink'), 5, 2);
        add_filter('govpub_field_meta_value', array($this, 'get_open_govpub_meta'), 5, 2);
        add_filter('govpub_field_type_value', array($this, 'get_open_govpub_type'), 5, 2);

        // Return the dates
        add_filter('govpub_field_created_at_value', array($this, 'get_open_govpub_created_at'), 5, 2);
        add_filter('govpub_field_updated_at_value', array($this, 'get_open_govpub_updated_at'), 5, 2);
    }

    public function register_rest_routes()
    {
        // Register the types rest route
        register_rest_route($this->namespace, '/types', array(
            'methods'   => 'GET',
            'callback'  => array($this, 'get_government_publication_types'),
            'args'      => get_open_govpub_types_api_args()
        ));

        // Register the search rest route
        register_rest_route($this->namespace, '/search', array(
            'methods'   => 'GET',
            'callback'  => array($this, 'search_government_publications'),
            'args'      => get_open_govpub_search_api_args()
        ));
    }

    public function get_open_govpub_identifier($c_value, $post)
    {
        return get_post_meta($post->ID, 'open_govpub_identifier', true);
    }

    public function get_open_govpub_permalink($c_value, $post)
    {
        return get_post_meta($post->ID, 'open_govpub_permalink', true);
    }

    public function get_open_govpub_meta($c_value, $post)
    {
        return get_post_meta($post->ID, 'open_govpub_meta', true);
    }

    public function get_open_govpub_type($c_value, $post)
    {
        // Get the types
        $types = get_the_terms($post, 'open_govpub_type');

        if (is_array($types) && isset($types[0]->name)) {
            return $types[0]->name;
        }

        // Default return the given value
        return $c_value;
    }

    public function get_open_govpub_created_at($c_value, $post)
    {
        return date_i18n('Y-m-d', strtotime($post->post_date));
    }

    public function get_open_govpub_updated_at($c_value, $post)
    {
        return date_i18n('Y-m-d', strtotime($post->post_modified));
    }

    public function get_government_publication_types($request)
    {
        // Set empty results variable
        $results    = array();

        // Get the params
        $hide_empty = $request->get_param('hide_empty');
        $hide_empty = ($hide_empty == 1 ? true : false);

        // Get the return type param
        $return_type = $request->get_param('return');

        // Get the types
        $types = get_terms(
            array(
                'taxonomy'      => 'open_govpub_type',
                'hide_empty'    => $hide_empty
            )
        );

        // Check if types exist
        if ($types && !empty($types)) {
            if ($return_type == 'object' || $return_type == 'array') {
                // Set types as results
                $results = $types;
            } else {
                foreach ($types as $type) {
                    // Add the type as result item
                    $results[$type->slug] = $type->name;
                }
            }
        }

        // Return the results
        return new WP_REST_Response($results, 200);
    }

    public function search_government_publications($request)
    {

        // Get globals
        global $post;

        // Get the params
        $type       = $request->get_param('open_govpub_type');

        // Set the query args
        $query_args = array(
            'post_type'         => 'open_govpub',
            'meta_query'        => array(
                'relation'          => 'OR',
                array(
                    'key'               => 'open_govpub_identifier',
                    'value'             => $request->get_param('s'),
                    'compare'           => 'LIKE'
                ),
                array(
                    'key'               => 'search_meta',
                    'value'             => $request->get_param('s'),
                    'compare'           => 'LIKE'
                )
            ),
            'posts_per_page'    => $request->get_param('limit'),
            'paged'             => $request->get_param('page'),
            'order'             => $request->get_param('order'),
            'orderby'           => $request->get_param('orderby')
        );

        // Check if filtering on type is needed
        if ($type) {
            // Set the taxonomy query
            $query_args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'open_govpub_type',
                    'field'    => 'name',
                    'terms'    => $type,
                ),
                array(
                    'taxonomy' => 'open_govpub_type',
                    'field'    => 'slug',
                    'terms'    => $type,
                ),
            );
        }

        // Set the results
        $data = array();

        // Initiate the query
        $wp_query = new WP_Query($query_args);

        if ($wp_query->have_posts()) {
            // Get the fields to return
            $fields = $this->get_return_fields($request);

            // Loop trough the results
            while ($wp_query->have_posts()) {
                $wp_query->the_post();

                // Loop trough the fields that need to be returned
                foreach ($fields as $field_name) {
                    // Get filtered field value
                    $value = apply_filters(
                        'govpub_field_' . $field_name . '_value',
                        $post->{$field_name},
                        $post
                    );

                    // Add the value to the results
                    $data[$post->ID][$field_name] = $value;
                }
            }
        }

        // Set the results
        $results = array(
            'pagination' => array(
                'found_posts'       => intval($wp_query->found_posts),
                'posts_per_page'    => intval($query_args['posts_per_page']),
                'paged'             => intval($query_args['paged']),
                'max_num_pages'     => intval($wp_query->max_num_pages)
            ),
            'data' => $data
        );
        wp_reset_postdata();

        // Return the results
        return new WP_REST_Response($results, 200);
    }

    public function get_default_return_fields()
    {
        // Return the fields
        return array(
            'identifier',
            'post_title',
            'permalink',
            'meta',
            'type',
            'created_at',
            'updated_at'
        );
    }

    public function get_return_fields($request)
    {
        // Get the default parameter
        $default    = $this->get_default_return_fields();

        // Get the fields by url parameter
        $the_fields = $request->get_param('fields');

        if ($the_fields) {
            $results    = array();
            $parts      = explode(':', $the_fields);

            // Sanitize the fields
            foreach ($parts as $field) {
                // Check if field is allowed
                if (in_array($field, $default)) {
                    $results[] = $field;
                }
            }

            // Return the fields
            return $results;
        }

        // Return the default fields
        return $default;
    }
}
