<?php

namespace SudwestFryslan\OpenGovernmentPublications;

/**
 * openGovpub Initial setup
 *
 * @since   1.0.0
 */
class Service
{
    private $service;
    private $query               = array();

    private $offset              = 1;
    private $max_records         = 10;

    private $result_limit        = 3000;
    private $total_found         = 0;

    private $sort_by             = false;
    private $sort_order          = 'ascending';

    private $has_limited_offset  = false;

    private $xml;
    private $last_record;

    /**
     * openGovpubService Constructor.
     */
    public function __construct($service)
    {

        // Set the service
        $this->service = $service;

        // Return object
        return $this;
    }

    public function set_offset($offset)
    {

        // Set the offset
        $this->offset = intval($offset) + 1;

        return $this;
    }

    public function set_max_records($max_records)
    {

        // Set the max_records
        $this->max_records = intval($max_records);

        // If limited offset isset re-run calculation
        if ($this->has_limited_offset) {
            $this->set_limited_offset();
        }

        return $this;
    }

    public function set_limited_offset()
    {

        // Set limited offset to true
        $this->has_limited_offset = true;

        // Set the offset
        $this->offset = ($this->result_limit - $this->max_records) + 1;

        return $this;
    }

    public function set_query($field, $value = false)
    {

        // Check if field is key and not an array
        if (!is_array($field)) {
            $field = array($field => $value);
        }

        // Overwrite the query
        $this->query = $field;

        // Set default sort
        $this->set_default_sort();

        return $this;
    }

    public function get_last_fieldname($name, $split = true, $s_char = ':')
    {

        // Get field parts
        $parts = $this->get_field($name, true);

        // Get last part of the path
        $last_part = end($parts);

        // Check if name needs to be split
        if ($split) {
            $last_parts = explode($s_char, $last_part);

            return end($last_parts);
        }

        return $last_part;
    }

    public function set_default_sort()
    {

        // Set the sorting
        $this->sort_by = $this->get_last_fieldname('created_at');

        return $this;
    }

    public function get_base_url()
    {

        // Get the base url
        $base_url = $this->service['url'];

        // Check if the default attributes exists
        if (isset($this->service['default_attr'])) {
            // Set the attributes
            $attributes = $this->service['default_attr'];

            // Add the attributes to the url
            $base_url   = add_query_arg($attributes, $base_url);
        }

        // Return the base url
        return $base_url;
    }

    public function is_field_array($fieldname)
    {

        // Check if field exists in mapping
        if (isset($this->service['mapping'][$fieldname])) {
            // Return if field is array
            return is_array($this->service['mapping'][$fieldname]);
        }

        return false;
    }

    public function get_field($fieldname, $parts = false)
    {

        // Set default
        $field = $fieldname;

        // Check if field exists in mapping
        if (isset($this->service['mapping'][$fieldname])) {
            // Set the field
            $field = $this->service['mapping'][$fieldname];
        }

        // Check if parts need to be returned
        if ($parts) {
            $field = explode('/', $field);
        }

        // Return the field
        return $field;
    }

    public function get_recursive_item($fields, $items = false, $string = false, $first_run = true)
    {

        // If no items set, set the xml as items
        if (!$items && $first_run) {
            $items = $this->xml;
        }

        // If no fields left, return the items
        if (empty($fields)) {
            return ($string && $items ? $items->__toString() : $items);
        }

        // Get first field
        $first_field = array_shift($fields);

        // Strip everything before :
        $first_field = end(explode(':', $first_field));

        // Get item by first field name
        $items = $items->{$first_field};

        // return the result
        return $this->get_recursive_item($fields, $items, $string, false);
    }

    public function filter_maping_item($fieldname, $value)
    {

        // If it is the max number of records item
        if ($fieldname == 'numberOfRecords') {
            // Set total records variable
            $this->total_found = $value;

            // If value is more then limit, return the limit
            if ($value > $this->result_limit) {
                return $this->result_limit;
            }
        }

        // Return the value
        return $value;
    }

    public function get_mapping_item($fieldname, $items = false, $string = false, $first_run = true)
    {

        // Check if field is array
        if ($this->is_field_array($fieldname)) {
            // Set empty results
            $results = array();

            // Get parent fields
            $parent_fields = $this->get_field($fieldname, false);

            // Loop trough items
            foreach ($parent_fields as $field_key => $map_string) {
                // Split path in parts
                $field_parts = explode('/', $map_string);

                // Get the item value and set the results
                $results[$field_key] = $this->get_recursive_item(
                    $field_parts,
                    $items,
                    $string,
                    $first_run
                );
            }

            return $results;
        }

        // Get recursive fields
        $fields = $this->get_field($fieldname, true);

        // Get the items
        $value = $this->get_recursive_item($fields, $items, $string, $first_run);

        // Filter the results value and return
        return $this->filter_maping_item($fieldname, $value);
    }

    public function get_query_string()
    {
        // Set queries array
        $queries = array();

        if (!empty($this->query)) {
            foreach ($this->query as $field => $value) {
                // Set default compare
                $compare = '=';

                // Check if value is array
                if (is_array($value)) {
                    // Set compare and value
                    $compare    = (isset($value['compare']) ? $value['compare'] : $compare);
                    $value      = (isset($value['value']) ? $value['value'] : '');
                }

                // Get the right fieldname
                $fieldname = $this->get_last_fieldname($field);

                // Add the query
                $queries[] = $fieldname . $compare . '"' . urlencode($value) . '"';
            }
        }

        // Create query string
        return implode(' and ', $queries);
    }

    public function get_sort_string()
    {

        // this has a sort by value
        if ($this->sort_by) {
            // Remove sort from string to prevent double appending
            $order = str_replace('sort.', '', $this->sort_order);

            return ' sortby ' . $this->sort_by . '/sort.' . $order;
        }

        return '';
    }

    public function get_encoded_url($url)
    {

        return str_replace(' ', '%20', $url);
    }

    public function get_request_url()
    {

        // Get the base url
        $base_url       = $this->get_base_url();

        // Set the query args
        $args           = array(
            $this->get_field('startRecord')     => $this->offset,
            $this->get_field('maximumRecords')  => $this->max_records,
            $this->get_field('query')           => $this->get_query_string(),
        );

        // Set the request url
        $request_url = add_query_arg($args, $base_url);

        // Get the sort string
        $sort_string = $this->get_sort_string();

        // Return the request url including the sort string
        return $request_url . $sort_string;
    }

    public function convert_xml($string)
    {

        // Remove colons in xml element name
        $string = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $string);

        // Convert string to XMl object
        $xml = simplexml_load_string($string);

        return $xml;
    }

    /**
     * Make the request
     */
    public function get_xml($url, $raw = false)
    {

        // Get cURL resource
        $curl = curl_init();

        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_URL             => $this->get_encoded_url($url),
        ));

        // Send the request & save response to $response
        $response = curl_exec($curl);


        // Close request to clear up some resources
        curl_close($curl);

        // Return the XML
        return ($raw ? $response : $this->convert_xml($response));
    }

    public function get_pagination_data()
    {

        // Get the records
        $records    = $this->get_mapping_item('records');

        // Return pagination array
        return array(
            'max_num_records'   => $this->get_mapping_item('numberOfRecords', false, true),
            'total_found'       => $this->total_found,
            'first_item'        => $this->offset,
            'last_item'         => (count($records) - 1) + $this->offset
        );
    }

    public function get_mapped_results()
    {

        // Set empty results array
        $results    = array();

        // Get the request url
        $url        = $this->get_request_url();

        // Get the xml
        $this->xml  = $this->get_xml($url);

        // Get the records
        $records    = $this->get_mapping_item('records');

        // Set the pagination data
        $results['pagination']      = $this->get_pagination_data();

        // Loop trought the records
        foreach ($records as $record) {
            // Set last record item variable
            $this->last_record = $this->get_mapped_record($record);

            // Add the mapped record as data record
            $results['data'][] = $this->last_record;
        }

        // Return the results
        return $results;
    }

    public function get_last_record()
    {

        // Return the last found record item
        return $this->last_record;
    }

    public function get_debug_results($raw = true)
    {

        // Set empty results array
        $results    = array();

        // Get the request url
        $url        = $this->get_request_url();

        // Return the xml
        return $this->get_xml($url, $raw);
    }

    public function print_debug_results($raw = true)
    {

        $results = $this->get_debug_results($raw);

        if ($raw) {
            // Set XML headers
            header("Content-type: text/xml");

            // Output raw xml
            echo $results;
        } else {
            echo '<pre>' . print_r($results, true) . '</pre>';

            die();
        }

        exit;
    }

    public function get_mapped_record($record)
    {

        // Get fields that need formating
        $created_at = $this->get_mapping_item('created_at', $record, true);
        $updated_at = $this->get_mapping_item('updated_at', $record, true);

        // Return the record
        return array(
            'identifier'    => $this->get_mapping_item('identifier', $record, true),
            'title'         => $this->get_mapping_item('title', $record, true),
            'permalink'     => $this->get_mapping_item('permalink', $record, true),
            'meta'          => $this->get_mapping_item('meta', $record, true),
            'created_at'    => date('Y-m-d', strtotime($created_at)),
            'updated_at'    => date('Y-m-d', strtotime($updated_at)),
        );
    }
}
