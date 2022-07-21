<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities;

class Publication extends ArrayValueObject
{
    public function identifier(): string
    {
        return (string) get_post_meta($this->ID, 'open_govpub_identifier', true);
    }

    public function permalink(): string
    {
        return (string) get_post_meta($this->ID, 'open_govpub_permalink', true);
    }

    public function meta(): array
    {
        return (array) get_post_meta($this->ID, 'open_govpub_meta', true);
    }

    public function type(): string
    {
        $types = get_the_terms($this->ID, 'open_govpub_type');

        if (! is_array($types)) {
            return '';
        }

        $first = reset($types);

        return isset($first->name) ? $first->name : '';
    }

    public function created_at(): string
    {
        return date_i18n('Y-m-d', strtotime($this->post_date));
    }

    public function updated_at(): string
    {
        return date_i18n('Y-m-d', strtotime($this->post_modified));
    }

    public function toArray(): array
    {
        return [
            'identifier'    => $this->identifier,
            'post_title'    => $this->post_title,
            'permalink'     => $this->permalink,
            'meta'          => $this->meta,
            'type'          => $this->type,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
