<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Support;

use SudwestFryslan\OpenGovernmentPublications\Container;

class PathResolver
{
    protected Container $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function theme(string $path): string
    {
        return $this->container->get('theme.path') . '/' . ltrim($path, '/');
    }

    public function plugin(string $path): string
    {
        return $this->container->get('plugin.path') . '/' . ltrim($path, '/');
    }

    public function view(string $name): string
    {
        return $this->plugin('/views/' . $name);
    }
}
