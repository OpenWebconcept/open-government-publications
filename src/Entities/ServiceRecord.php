<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities;

class ServiceRecord extends ArrayValueObject
{
    protected ?int $postId;

    /**
     * Mutated value
     * @return string
     */
    public function created_at(): string
    {
        return (string) ($this->created ? $this->created->format('Y-m-d') : '');
    }

    /**
     * Mutated value
     * @return string
     */
    public function updated_at(): string
    {
        return (string) ($this->updated ? $this->created->format('Y-m-d') : '');
    }

    public function getPostArray(): array
    {
        return [
            'post_title'    => wp_strip_all_tags($this->title ?? ''),
            'post_date'     => $this->created_at,
            'post_modified' => $this->updated_at,
            'post_type'     => 'open_govpub',
            'post_status'   => 'publish'
        ];
    }

    public function getPostId(): ?int
    {
        if (isset($this->postId)) {
            return $this->postId;
        }

        global $wpdb;

        $postId = $wpdb->get_var($wpdb->prepare("
            SELECT posts.ID
            FROM
                `{$wpdb->posts}` AS posts
                JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
            WHERE
                postmeta.meta_key = 'open_govpub_identifier'
                AND postmeta.meta_value = '%s'
                AND posts.post_type = 'open_govpub'
            LIMIT 1
        ", $this->identifier));

        $this->postId = is_numeric($postId) ? intval($postId) : null;

        return $this->postId;
    }

    public function exists(): bool
    {
        if (! $this->identifier) {
            return false;
        }

        return $this->getPostId() !== null;
    }
}
