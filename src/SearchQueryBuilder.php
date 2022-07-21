<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use WP_REST_Request;

class SearchQueryBuilder
{
    protected array $defaultArguments = [
        'post_type'         => 'open_govpub',
    ];

    protected array $query;

    public function __construct(array $query = [])
    {
        $this->query = array_merge($this->defaultArguments, $query);
    }

    public static function fromRestRequest(WP_REST_Request $request): self
    {
        $builder = new self();
        $builder->limit((int) $request->get_param('limit'))
            ->page((int) $request->get_param('page'))
            ->order(
                (string) $request->get_param('orderby'),
                (string) $request->get_param('order')
            )->metaQuery([
                'relation'      => 'OR',
                [
                    'key'       => 'open_govpub_identifier',
                    'value'     => $request->get_param('s'),
                    'compare'   => 'LIKE'
                ], [
                    'key'       => 'search_meta',
                    'value'     => $request->get_param('s'),
                    'compare'   => 'LIKE'
                ]
            ]);

        if ($request->has_param('open_govpub_type')) {
            $builder->taxQuery([
                'relation' => 'OR',
                [
                    'taxonomy' => 'open_govpub_type',
                    'field'    => 'name',
                    'terms'    => $request->get_param('open_govpub_type'),
                ], [
                    'taxonomy' => 'open_govpub_type',
                    'field'    => 'slug',
                    'terms'    => $request->get_param('open_govpub_type'),
                ],
            ]);
        }

        return $builder;
    }

    public function limit(int $limit): self
    {
        return $this->setArgument('posts_per_page', $limit);
    }

    public function page(int $pageNo): self
    {
        return $this->setArgument('paged', $pageNo);
    }

    public function order(string $column, string $direction): self
    {
        return $this->setArgument('orderby', $column)
            ->setArgument('order', $direction);
    }

    public function metaQuery(array $query): self
    {
        return $this->setArgument('meta_query', $query);
    }

    public function taxQuery(array $query): self
    {
        return $this->setArgument('tax_query', $query);
    }

    public function setArgument(string $argument, $value): self
    {
        $this->query[$argument] = $value;

        return $this;
    }

    public function getArguments(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return $this->query;
    }
}
