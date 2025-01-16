<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use DateTime;
use Exception;
use SimpleXMLElement;
use SudwestFryslan\OpenGovernmentPublications\Entities\ServiceRecord;
use SudwestFryslan\OpenGovernmentPublications\Entities\Service as ServiceEntity;

class Service
{
    protected ServiceEntity $service;
    protected array $query = [];

    protected int $offset = 1;
    protected int $max_records = 10;

    protected int $result_limit = 3000;
    protected int $total_found = 0;

    protected $sort_by = false;
    protected $sort_order = 'ascending';

    protected $has_limited_offset  = false;

    protected $xml;
    protected $last_record;

    public function __construct(ServiceEntity $service)
    {
        $this->service = $service;
    }

    public function set_offset(int $offset): self
    {
        $this->offset = $offset + 1;

        return $this;
    }

    public function set_max_records(int $max_records): self
    {
        $this->max_records = $max_records;

        // If limited offset isset re-run calculation
        if ($this->has_limited_offset) {
            $this->set_limited_offset();
        }

        return $this;
    }

    public function set_limited_offset(): self
    {
        $this->has_limited_offset = true;
        $this->offset = ($this->result_limit - $this->max_records) + 1;

        return $this;
    }

    public function set_query(array $query): self
    {
        $this->query = $query;

        $this->set_default_sort();

        return $this;
    }

    public function set_default_sort(): self
    {
        $this->sort_by = $this->getLastFieldname('date');

        return $this;
    }

    /**
     * @return (ServiceRecord[]|mixed)[]
     *
     * @psalm-return array{data: list<ServiceRecord>, pagination: mixed}
     */
    public function get_mapped_results(): array
    {
        $url = $this->getRequestUrl();
        $this->xml = $this->doRequest($url);

        $records = $this->get_mapping_item('records');

        $results = ['data' => [], 'pagination' => $this->get_pagination_data()];

        if (empty($records)) {
            return $results;
        }

        foreach ($records as $record) {
            $this->last_record = $this->getMappedRecord($record);

            // Add the mapped record as data record
            $results['data'][] = $this->last_record;
        }

        return $results;
    }

    public function get_last_record()
    {
        return $this->last_record;
    }

    public function get_pagination_data(): array
    {
        // $records    = $this->get_mapping_item('records');

        return [
            'max_num_records'   => $this->get_mapping_item('numberOfRecords', false, true),
            'total_found'       => $this->total_found,
            'first_item'        => $this->offset,
            // 'last_item'         => (count($records) - 1) + $this->offset
        ];
    }

    public function getLastFieldname($name, $split = true, $splitCharacter = ':')
    {
        $parts = $this->getField($name, true);
        $lastPart = end($parts);

        if ($split) {
            $lastParts = explode($splitCharacter, $lastPart);

            return end($lastParts);
        }

        return $lastPart;
    }

    protected function getMappedRecord(SimpleXMLElement $record): ServiceRecord
    {
        $created_at = $this->get_mapping_item('created_at', $record, true);
        $updated_at = $this->get_mapping_item('updated_at', $record, true);

        return new ServiceRecord([
            'identifier'    => $this->get_mapping_item('identifier', $record, true),
            'title'         => $this->get_mapping_item('title', $record, true),
            'permalink'     => $this->get_mapping_item('permalink', $record, true),
            'meta'          => $this->get_mapping_item('meta', $record, true),
            'created'       => new DateTime($created_at),
            'updated'       => new DateTime($updated_at),
        ]);
    }

    /**
     * @param SimpleXMLElement|false $items
     */
    protected function get_mapping_item(string $fieldname, $items = false, bool $string = false, $first_run = true)
    {
        if (! $this->isFieldArray($fieldname)) {
            $fields = $this->getField($fieldname, true);
            $value = $this->get_recursive_item($fields, $items, $string, $first_run);

            return $this->filter_maping_item($fieldname, $value);
        }

        $results = [];
        $parentFields = $this->getField($fieldname, false);

        foreach ($parentFields as $fieldKey => $mapString) {
            $fieldParts = explode('/', $mapString);

            $results[$fieldKey] = $this->get_recursive_item(
                $fieldParts,
                $items,
                $string,
                $first_run
            );
        }

        return $results;
    }

    /**
     * @param  string       $fieldname
     * @param  bool         $inParts
     * @return string|array
     */
    protected function getField(string $fieldname, bool $inParts = false)
    {
        $field = $this->service->getMappingOf($fieldname, $fieldname);

        if ($inParts) {
            $field = explode('/', $field);
        }

        return $field;
    }

    protected function get_recursive_item(array $fields, $items = false, $string = false, bool $first_run = true)
    {
        if (! $items && $first_run) {
            $items = $this->xml;
        }

        // If no fields left, return the items
        if (empty($fields)) {
            return $string && $items ? $items->__toString() : $items;
        }

        // Get first field
        $first_field = array_shift($fields);

        // Strip everything before :
        $first_field = explode(':', $first_field);
        $first_field = end($first_field);

        // Get item by first field name
        $items = $items->{$first_field};

        // return the result
        return $this->get_recursive_item($fields, $items, $string, false);
    }

    protected function isFieldArray($fieldname): bool
    {
        $mapping = $this->service->getMappingOf($fieldname);

        return is_array($mapping);
    }

    protected function filter_maping_item(string $fieldname, $value)
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

        return $value;
    }

    /**
     * @todo move to separate URL builder
     * @return string
     */
    protected function getRequestUrl(): string
    {
        $requestUrl = add_query_arg([
            $this->getField('startRecord')     => $this->offset,
            $this->getField('maximumRecords')  => $this->max_records,
            $this->getField('query')           => urlencode($this->getQueryUrlParameter()),
        ], $this->service->getUrl());

        return $requestUrl . $this->getSortString();
    }

    /**
     * @todo move to separate URL builder
     * @return string
     */
    protected function getQueryUrlParameter(): string
    {
        if (empty($this->query)) {
            return '';
        }

        $queries = [];
        foreach ($this->query as $field => $value) {
            $comparator = '=';

            if (is_array($value)) {
                $comparator = $value['compare'] ?? $comparator;
                $value = $value['value'] ?? '';
            }

            $queries[] = sprintf(
                '%s%s"%s"',
                $this->getLastFieldname($field),
                $comparator,
                $value
            );
        }

        return implode(' and ', $queries);
    }

    /**
     * @legacy
     * @return string
     */
    protected function getSortString(): string
    {
        if ($this->sort_by) {
            // Remove sort from string to prevent double appending
            $order = str_replace('sort.', '', $this->sort_order);

            return urlencode(' sortby ' . $this->sort_by . '/sort.' . $order);
        }

        return '';
    }

    /**
     * @todo Move to separate request handler
     * @param  string                        $url
     * @param  bool                          $raw
     * @return false|string|SimpleXMLElement
     */
    protected function doRequest($url, $raw = false)
    {
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        if (($response['response']['code'] ?? 400) >= 400) {
            throw new Exception("Invalid response from server @ " . $url);
        }

        return $raw ? $response['body'] : $this->convertToXml($response['body']);
    }

    /**
     * @todo Move to separate converter
     * @param  string           $string
     * @return SimpleXMLElement
     */
    protected function convertToXml($string): SimpleXMLElement
    {
        $string = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $string);

        $xml = simplexml_load_string($string);

        if (! $xml) {
            throw new Exception("Unable to load XML data");
        }

        return $xml;
    }
}
