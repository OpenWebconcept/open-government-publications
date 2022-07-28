<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities;

use WP_Term;

class Service extends ArrayValueObject
{
    public function getTerm(): ?WP_Term
    {
        $term = get_term_by('slug', $this->identifier, 'open_govpub_type');

        if (! empty($term)) {
            return $term;
        }

        $title = $this->getValue('title', ucfirst($this->identifier));

        $term = wp_insert_term($title, 'open_govpub_type', [
            'slug' => $this->identifier,
        ]);

        if (is_wp_error($term)) {
            throw new \Exception($term->get_error_message());
        }

        return get_term_by('term_id', $term['term_id'] ?? 0, '', 'OBJECT') ?: null;
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function getMappingOf(string $fieldName, string $default = '')
    {
        return $this->mapping[$fieldName] ?? $default;
    }

    public function getUrl(): string
    {
        if ($this->default_attr) {
            return (string) add_query_arg($this->default_attr, $this->url);
        }

        return (string) $this->url;
    }
}
