<?php

use SudwestFryslan\OpenGovernmentPublications\Entities\Service;

return [
    'types.api.args'    => [
        'hide_empty'        => [
            'type'              => 'integer',
            'default'           => 1,
            'sanitize_callback' => 'absint',
        ],
        'return'            => [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]
    ],
    'search.api.args'   => [
        's'                 => [
            'description'       => 'Limit results to those matching a string.',
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        'open_govpub_type'   => [
            'description'       => 'Find publications that are a member of this term (expects a slug)',
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        'limit'             => [
            'type'              => 'integer',
            'default'           => 20,
            'sanitize_callback' => 'absint',
        ],
        'page'              => [
            'type'              => 'integer',
            'default'           => 1,
            'sanitize_callback' => 'absint',
        ],
        'orderby'           => [
            'type'              => 'string',
            'default'           => 'date',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        'order'             => [
            'type'              => 'string',
            'default'           => 'DESC',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        'fields'            => [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ],
    ],
    'services.config'   => function () {
        return [
            'bekendmaking'  => new Service([
                'identifier'    => 'bekendmaking',
                'title'         => 'Bekendmaking',
                'url'           => 'https://repository.overheid.nl/sru',
                'default_attr'  => [
                    'version'       => '2.0',
                ],
                'mapping'   => [
                    'creator'           => 'dt.creator',
                    'date'              => 'dt.date',
                    'startRecord'       => 'startRecord',
                    'maximumRecords'    => 'maximumRecords',
                    'query'             => 'query',
                    'numberOfRecords'   => 'numberOfRecords',
                    'records'           => 'records/record',
                    'identifier'        => 'recordData/gzd/originalData/meta/owmskern/identifier',
                    'title'        => 'recordData/gzd/originalData/meta/owmskern/title',
                    'permalink'         => 'recordData/gzd/enrichedData/preferredUrl',
                    'meta'              => [
                        'subject'           => 'recordData/gzd/originalData/meta/owmsmantel/subject',
                        'organisationtype'  => 'recordData/gzd/originalData/meta/tpmeta/organisatietype',
                        'publicationname'   => 'recordData/gzd/originalData/meta/tpmeta/publicatienaam',
                    ],
                    'created_at'        => 'recordData/gzd/originalData/meta/owmsmantel/date',
                    'updated_at'        => 'recordData/gzd/originalData/meta/owmskern/modified',
                ],
            ]),
            'regelingen_verordeningen' => new Service([
                'identifier'    => 'regelingen_verordeningen',
                'title'         => 'Regelingen en verordeningen',
                'url'           => 'https://zoekservice.overheid.nl/sru/Search',
                'default_attr'  => [
                    'version'       => '1.2',
                    'operation'     => 'searchRetrieve',
                    'x-connection'  => 'cvdr',
                ],
                'mapping'       => [
                    'creator'           => 'creator',
                    'date'              => 'issued',
                    'startRecord'       => 'startRecord',
                    'maximumRecords'    => 'maximumRecords',
                    'query'             => 'query',
                    'numberOfRecords'   => 'numberOfRecords',
                    'records'           => 'records/record',
                    'identifier'        => 'recordData/gzd/originalData/meta/owmskern/identifier',
                    'title'             => 'recordData/gzd/originalData/meta/owmskern/title',
                    'permalink'         => 'recordData/gzd/enrichedData/preferred_url',
                    'meta'              => [
                        'subject'           => 'recordData/gzd/originalData/meta/owmsmantel/subject',
                        'subject_alt'       => 'recordData/gzd/originalData/meta/owmsmantel/alternative',
                        'organisationtype'  => 'recordData/gzd/enrichedData/organisatietype',
                        'publicationname'   => 'recordData/gzd/originalData/meta/owmsmantel/isFormatOf',
                        'betreft'           => 'recordData/gzd/originalData/meta/cvdripm/betreft',
                        'kenmerk'           => 'recordData/gzd/originalData/meta/cvdripm/kenmerk',
                    ],
                    'created_at'        => 'recordData/gzd/originalData/meta/owmsmantel/issued',
                    'updated_at'        => 'recordData/gzd/originalData/meta/owmskern/modified',
                ],
            ]),
        ];
    },
];
