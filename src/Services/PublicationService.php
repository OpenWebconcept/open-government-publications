<?php

namespace SudwestFryslan\OpenGovernmentPublications\Services;

use Throwable;
use Exception;
use SudwestFryslan\OpenGovernmentPublications\Entities\Service;
use SudwestFryslan\OpenGovernmentPublications\Entities\Publication;
use SudwestFryslan\OpenGovernmentPublications\Entities\ServiceRecord;

class PublicationService
{
    public function create(ServiceRecord $record): Publication
    {
        $postId = wp_insert_post($record->getPostArray());

        if (is_wp_error($postId)) {
            throw new Exception($postId->get_error_message());
        }

        return new Publication(get_post($postId));
    }

    public function update(ServiceRecord $record): Publication
    {
        $update = wp_update_post(array_merge(
            ['ID' => $record->getPostId()],
            $record->getPostArray()
        ));

        if (is_wp_error($update)) {
            throw new Exception($update->get_error_message());
        }

        return new Publication(get_post($record->getPostId()));
    }

    /**
     * @return true
     */
    public function saveMeta(Publication $publication, Service $service, ServiceRecord $record): bool
    {
        update_post_meta($publication->ID, 'open_govpub_identifier', $record->identifier);
        update_post_meta($publication->ID, 'open_govpub_permalink', $record->permalink);
        update_post_meta($publication->ID, 'open_govpub_meta', $record->meta);

        $publication->linkToService($service);

        return true;
    }

    /**
     * @return true
     */
    public function saveSearchmeta(Publication $publication, ServiceRecord $record): bool
    {
        update_post_meta(
            $publication->ID,
            'search_meta',
            $publication->getValue('post_title', '') . implode($record->getValue('meta', []))
        );

        return true;
    }
}
