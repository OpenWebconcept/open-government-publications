<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use WP_Query;

class Import implements ServiceProviderInterface
{
    protected Container $container;
    protected int $max_import = 50;
    protected int $transient_time = 10;
    protected int $limit_import = 3000;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        // Plan the import task
        add_action('open_govpub_check_import_publications', array($this, 'check_import_publications'));

        // Run the import task
        add_action('open_govpub_task_import_publications', array($this, 'run_import_publications'));

        // Set the ajax import action
        add_action('wp_ajax_import_open_govpub', array($this, 'import_open_govpub_data'));
    }

    public function get_service_last_date($src_id, $last_dates = false, $default = '1990-01-01')
    {
        // Check if last dates is passed
        if (!$last_dates) {
            $last_dates = get_open_govpub_option('last_import_dates');
        }

        // If last dates is an array and it is not empty
        if (is_array($last_dates) && !empty($last_dates)) {
            return (isset($last_dates[$src_id]) ? $last_dates[$src_id] : $default);
        }

        // Return the default option
        return $default;
    }

    public function save_services_last_dates()
    {
        // Get previous last dates
        $last_dates = get_open_govpub_option('last_import_dates');
        $last_dates = ($last_dates && is_array($last_dates) ? $last_dates : array());

        // Get all current imports
        $current_imports = get_open_govpub_option('current_import');

        if (is_array($current_imports)) {
            // Loop trough the current imports
            foreach ($current_imports as $service_id => $current_import) {
                // If the status more then teh max amount found the last date can be today
                if ($current_import['status'] >= $current_import['total_found']) {
                    // Set date
                    $last_dates[$service_id] = date('Y-m-d');
                } else {
                    // Set the date as last record date
                    $last_dates[$service_id] = $current_import['last_record_date'];
                }
            }
        }

        // Save the last services dates
        update_open_govpub_option('last_import_dates', $last_dates);

        // Return the new last dates
        return $last_dates;
    }

    public function create_current_services_import($last_dates)
    {
        $creator = get_open_govpub_setting('creator');

        // Check if a creator isset
        if ($creator && !empty($creator)) {
            // Get services config
            $services       = get_open_govpub_service_config();
            $current_import = array();
            $max_items      = $total_items = 0;

            // Set default query args
            $query          = array( 'creator' => $creator );

            // Set offset and max records
            $offset         = $this->limit_import;
            $max_records    = 1;

            // Loop trough services
            foreach ($services as $service_id => $the_service) {
                // Get the last service import date
                $last_date = $this->get_service_last_date($service_id, $last_dates);

                // Set the service query args
                $query['created_at'] = array(
                    'value'             => date_i18n('Y-m-d', strtotime($last_date)),
                    'compare'           => '>='
                );

                // Load the service
                $service = new Service($the_service);
                $service->set_limited_offset()->set_max_records(1);
                $service->set_query($query);

                // Get the results
                $results = $service->get_mapped_results();

                // Check if pagination items exists
                if (isset($results['pagination']['max_num_records'])) {
                    // Set found and total items
                    $found_items = intval($results['pagination']['max_num_records']);
                    $total_found = intval($results['pagination']['total_found']);

                    // Check if any items found
                    if ($found_items > 0) {
                        // Add the current service item
                        $current_import[$service_id] = array(
                            'status'        => 0,
                            'max_num'       => $found_items,
                            'total_found'   => $total_found,
                            'date_offset'   => $last_date
                        );

                        // Add found items count to total
                        $max_items      += $found_items;
                        $total_items    += $total_found;
                    }
                }
            }

            // Return the results
            return array(
                'current_import'    => $current_import,
                'total_import'      => array(
                    'status'            => 0,
                    'max_num'           => $max_items,
                    'total_num'         => $total_items,
                    'import_date'       => date_i18n('Y-m-d H:i:s')
                )
            );
        }

        return false;
    }

    public function check_import_publications()
    {
        // Check if import isn't locked
        if (!is_govpub_import_check_locked()) {
            // Lock import for 2 minutes so no other script will run simultaneously
            set_transient('govpub_import_check_locked', true, $this->transient_time);

            // Get total import
            $total_import = get_open_govpub_option('total_import');

            // Check if no current import exists
            if (!$total_import) {
                // Get last import date
                $last_dates         = get_open_govpub_option('last_import_dates');

                // Create import data
                $import         = $this->create_current_services_import($last_dates);

                if ($import && isset($import['current_import'])) {
                    // Save the options
                    update_open_govpub_option('current_import', $import['current_import']);
                    update_open_govpub_option('total_import', $import['total_import']);

                    // Set new total_import var
                    $total_import = $import['total_import'];
                }
            }

            // Unlock import check
            delete_transient('govpub_import_check_locked');

            // Return the total import
            return $total_import;
        } else {
            // Give error message
            echo __('Another import process is running at this time, try again later');
            exit;
        }
    }

    public function get_current_import_service()
    {
        // Get all current imports
        $current_imports = get_open_govpub_option('current_import');

        // Loop trough the services to find a service that needs importing
        foreach ($current_imports as $service_id => $c_import) {
            // If status is less then the max number
            if ($c_import['status'] < $c_import['max_num']) {
                // Get services config
                $services = get_open_govpub_service_config();

                // Return the service
                return array(
                    'service_id'    => $service_id,
                    'service'       => $services[$service_id]
                ) + $c_import;
            }
        }

        // Default return false
        return false;
    }

    public function recreate_total_import_by_current_import()
    {
        // Get import variable
        $current_imports    = get_open_govpub_option('current_import');
        $total_import       = get_open_govpub_option('total_import');

        // Check if total import status isset
        if (isset($total_import['status'])) {
            // Reset values
            $total_import['status']     = 0;
            $total_import['max_num']    = 0;

            // Loop trough current imports
            foreach ($current_imports as $current_import) {
                // Check if current import has status
                if (isset($current_import['status'])) {
                    // Add numbers
                    $total_import['status']     += $current_import['status'];
                    $total_import['max_num']    += $current_import['max_num'];
                }
            }
        }

        // Return the total import
        return $total_import;
    }

    public function get_service_term_id($service_slug)
    {
        // Get the term
        $term = get_term_by('slug', $service_slug, 'open_govpub_type');

        // Check if term is found
        if ($term) {
            // Return the term object
            return $term->term_id;
        } else {
            // Get service config item
            $service    = get_open_govpub_service_config($service_slug);

            $title      = (isset($service['title']) ? $service['title'] : ucfirst($service_slug));
            $desc       = (isset($service['desc']) ? $service['desc'] : '');

            // Create the term
            $term = wp_insert_term($title, 'open_govpub_type', array(
                'description'   => $desc,
                'slug'          => $service_slug,
            ));

            // Return the just created term id
            return ( $term && isset($term['term_id']) ? $term['term_id'] : false );
        }

        // Default return false
        return false;
    }

    public function get_govpub_post_id($result)
    {

        // Set identifier
        $identifier = $result['identifier'];

        // Set false as default post id
        $post_id    = false;

        // Set query args
        $args       = array(
            'post_type'     =>  'open_govpub',
            'meta_query'    =>  array(
                array(
                    'key'   => 'open_govpub_identifier',
                    'value' => $identifier
                )
            ),
            'posts_per_page' => 1
        );

        // Setup WP_Query object by identifier
        $the_query = new WP_Query($args);

        // Check if post found and set that post id
        if ($the_query->have_posts()) {
            $the_query->the_post();

            // Set the post id
            $post_id = get_the_ID();
        }

        // Reset the postdata
        wp_reset_postdata();

        // Return the post id
        return $post_id;
    }

    public function get_post_data_array($result)
    {

        // Return the post data array
        return array(
            'post_title'    => wp_strip_all_tags($result['title']),
            'post_date'     => $result['created_at'],
            'post_modified' => $result['updated_at'],
            'post_type'     => 'open_govpub',
            'post_status'   => 'publish'
        );
    }

    public function save_govpub_post_meta($post_id, $result, $service_slug)
    {

        // Get the term id
        $term_id = $this->get_service_term_id($service_slug);

        // Attach the term to the post
        wp_set_object_terms($post_id, $term_id, 'open_govpub_type');

        // Check if meta exists in result
        if (isset($result['meta'])) {
            // Save the meta
            update_post_meta($post_id, 'open_govpub_meta', $result['meta']);
        }
    }

    public function save_govpub_search_meta($post_id, $result, $post_data)
    {

        $search_meta = array();

        // If post title available, add it
        if (isset($post_data['post_title'])) {
            $search_meta[] = $post_data['post_title'];
        }

        // If meta available, add it
        if (isset($result['meta']) && is_array($result['meta'])) {
            $search_meta = array_merge($search_meta, $result['meta']);
        }

        // Create a search string
        $search_string = implode(' ', $search_meta);

        // Save the meta
        update_post_meta($post_id, 'search_meta', $search_string);
    }

    public function create_govpub_post($result, $service_slug)
    {

        // Create post data array
        $post_data = $this->get_post_data_array($result);

        // Insert the post into the database
        $post_id = wp_insert_post($post_data);

        // Save the identifier and permalink
        update_post_meta($post_id, 'open_govpub_identifier', $result['identifier']);
        update_post_meta($post_id, 'open_govpub_permalink', $result['permalink']);

        // Save the meta and term data
        $this->save_govpub_post_meta($post_id, $result, $service_slug);

        // Save a search meta that the API can use to search in
        $this->save_govpub_search_meta($post_id, $result, $post_data);
    }

    public function update_govpub_post($post_id, $result, $service_slug)
    {

        // get post data array
        $post_data          = $this->get_post_data_array($result);

        // Add the post id to the post data
        $post_data['ID']    = $post_id;

        // Update the post into the database
            wp_update_post($post_data);

            // Save the meta and term data
        $this->save_govpub_post_meta($post_id, $result, $service_slug);

        // Save a search meta that the API can use to search in
        $this->save_govpub_search_meta($post_id, $result, $post_data);
    }

    public function save_govpub_post($result, $service_slug)
    {

        // Get current post
        $c_post_id = $this->get_govpub_post_id($result);

        if ($c_post_id) {
            // Update current post item
            $this->update_govpub_post($c_post_id, $result, $service_slug);
        } else {
            // Create a new post item
            $this->create_govpub_post($result, $service_slug);
        }

        // TODO: save the custom meta data (open_govpub_meta)
    }

    public function save_govpub_posts($results, $service_slug)
    {

        $saved_i = 0;

        // Loop trough the results
        foreach ($results as $result) {
            // Create or update the post
            $this->save_govpub_post($result, $service_slug);

            $saved_i++;
        }

        return $saved_i;
    }

    public function import_by_import_service($import_service)
    {

        // echo '<pre>' . print_r($import_service, true) .'</pre>'; die();

        // Get creator
        $creator = get_open_govpub_setting('creator');

        // Check if a creator isset
        if ($creator && !empty($creator)) {
            // Set the service
            $the_service    = $import_service['service'];
            $service_id     = $import_service['service_id'];

            // Set query
            $query          = array(
                'creator'       => $creator,
                'created_at'        => array(
                    'value'         => date_i18n('Y-m-d', strtotime($import_service['date_offset'])),
                    'compare'       => '>='
                )
            );

            // Load the service
            $service = new Service($the_service);

            // Set offset and max record
            $service->set_offset($import_service['status']);
            $service->set_max_records($this->max_import);

            // Set the query
            $service->set_query($query);

            // Get the results
            $results = $service->get_mapped_results();

            // Get the last record item
            $last_record        = $service->get_last_record();
            $last_record_date   = (isset($last_record['created_at']) ? $last_record['created_at'] : '');

            // Check if any result data is fetched
            if (isset($results['data'])) {
                // Create or update the posts
                $saved_i = $this->save_govpub_posts($results['data'], $service_id);

                // The results
                return array(
                    'imported'          => $saved_i,
                    'max_num_records'   => $results['pagination']['max_num_records'],
                    'total_found'       => $results['pagination']['total_found'],
                    'date_offset'       => $import_service['date_offset'],
                    'last_record_date'  => $last_record_date
                );
            }
        }

        return false;
    }

    public function run_import_publications()
    {

        // Check if import isn't locked
        if (!is_govpub_import_locked()) {
            // Lock import for 2 minutes so no other script will run simultaneously
            set_transient('govpub_import_locked', true, $this->transient_time);

            // Get total import
            $total_import = get_open_govpub_option('total_import');

            // Check if current import exists
            if ($total_import) {
                // Check if import completed
                if ($total_import['status'] >= $total_import['max_num']) {
                    // Save the services last date
                    $this->save_services_last_dates();

                    // Set last import dates
                    update_open_govpub_option('last_import_date', $total_import['import_date']);

                    // Remove total import data
                    delete_open_govpub_option('total_import');

                    // Set empty total import
                    $total_import = false;
                } else {
                    // Get service that need to be run
                    $service    = $this->get_current_import_service();

                    // Import and get amount of imported items
                    $results    = $this->import_by_import_service($service);

                    // Check if something imported
                    if ($results && isset($results['imported'])) {
                        // Set variables
                        $imported_i     = $service['status'] + $results['imported'];
                        $max_results    = $results['max_num_records'];

                        // Create new status array
                        $new_status = array(
                            'status'            => $imported_i,
                            'max_num'           => $max_results,
                            'total_found'       => $results['total_found'],
                            'date_offset'       => $results['date_offset'],
                            'last_record_date'  => $results['last_record_date']
                        );

                        // Update the new status
                        update_open_govpub_sub_option('current_import', $service['service_id'], $new_status);

                        // Recount import data
                        $total_import = $this->recreate_total_import_by_current_import();

                        // Update the total import
                        update_open_govpub_option('total_import', $total_import);
                    }
                }
            }

            // Unlock import
            delete_transient('govpub_import_locked');

            // Return the total import
            return $total_import;
        } else {
            // Give error message
            echo __('Another import process is running at this time, try again later');
            exit;
        }
    }

    public function import_open_govpub_data()
    {

        // Set default result
        $results = array(
            'status' => 'done'
        );

        // Check if not allready checked for needed update
        if (!isset($_REQUEST['checked']) || $_REQUEST['checked'] == 0) {
            // Check if import needed
            $total_import = $this->check_import_publications();
        } else {
            // Run import
            $total_import = $this->run_import_publications();
        }

        // Check if total import ran
        if ($total_import && isset($total_import['status'])) {
            // Progress
            $progress   = $total_import['status'] / $total_import['max_num'];
            $progress   = floor($progress * 10000) / 100; // Floor 2 decimals

            // Set results
            $results    = array(
                'status'        => 'running',
                'progress'      => $progress,
                'details'       => $total_import,
                'import_string' => get_open_govpub_current_import_string()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($results);

        exit;
    }
}
