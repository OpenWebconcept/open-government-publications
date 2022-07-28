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
                'url'           => 'https://zoek.officielebekendmakingen.nl/sru/Search',
                'default_attr'  => [
                    'version'       => '1.2',
                    'operation'     => 'searchRetrieve'
                ],
                'mapping'   => [
                    'startRecord'       => 'startRecord',
                    'maximumRecords'    => 'maximumRecords',
                    'query'             => 'query',
                    'numberOfRecords'   => 'numberOfRecords',
                    'records'           => 'records/record',
                    'identifier'        => 'recordData/gzd/originalData/overheidop:meta/overheidop:owmskern/dcterms:identifier',
                    'title'             => 'recordData/gzd/originalData/overheidop:meta/overheidop:owmskern/dcterms:title',
                    'permalink'         => 'recordData/gzd/enrichedData/url',
                    'meta'              => [
                        'subject'           => 'recordData/gzd/originalData/overheidop:meta/overheidop:owmsmantel/dcterms:subject',
                        'organisationtype'  => 'recordData/gzd/originalData/overheidop:meta/overheidop:opmeta/overheid:organisationtype',
                        'publicationname'   => 'recordData/gzd/originalData/overheidop:meta/overheidop:opmeta/overheid:publicationname',
                    ],
                    'created_at'        => 'recordData/gzd/originalData/overheidop:meta/overheidop:owmsmantel/dcterms:date',
                    'updated_at'        => 'recordData/gzd/originalData/overheidop:meta/overheidop:owmskern/dcterms:modified',
                ]
            ]),
            'regelingen_verordeningen' => new Service([
                'identifier'    => 'regelingen_verordeningen',
                'title'         => 'Regelingen en verordeningen',
                'url'           => 'http://zoekdienst.overheid.nl/sru/Search',
                'default_attr'  => [
                    'version'       => '1.2',
                    'operation'     => 'searchRetrieve',
                    'x-connection'  => 'cvdr'
                ],
                'mapping'       => [
                    'startRecord'       => 'startRecord',
                    'maximumRecords'    => 'maximumRecords',
                    'query'             => 'query',
                    'numberOfRecords'   => 'numberOfRecords',
                    'records'           => 'records/record',
                    'identifier'        => 'recordData/gzd/originalData/overheidrg:meta/owmskern/dcterms:identifier',
                    'title'             => 'recordData/gzd/originalData/overheidrg:meta/owmskern/dcterms:title',
                    'permalink'         => 'recordData/gzd/enrichedData/publicatieurl_xhtml',
                    'meta'              => [
                        'subject'           => 'recordData/gzd/originalData/overheidrg:meta/owmsmantel/dcterms:subject',
                        'subject_alt'       => 'recordData/gzd/originalData/overheidrg:meta/cvdripm/overheidrg:onderwerp',
                        'organisationtype'  => 'recordData/gzd/enrichedData/organisatietype',
                        'publicationname'   => 'recordData/gzd/originalData/overheidrg:meta/owmsmantel/dcterms:isFormatOf',
                        'betreft'           => 'recordData/gzd/originalData/overheidrg:meta/cvdripm/overheidrg:betreft',
                        'kenmerk'           => 'recordData/gzd/originalData/overheidrg:meta/cvdripm/overheidrg:kenmerk',
                    ],
                    'created_at'        => 'recordData/gzd/originalData/overheidrg:meta/owmsmantel/dcterms:issued',
                    'updated_at'        => 'recordData/gzd/originalData/overheidrg:meta/owmskern/dcterms:modified',
                ]
            ])
        ];
    }
];
