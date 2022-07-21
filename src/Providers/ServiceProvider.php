<?php

namespace SudwestFryslan\OpenGovernmentPublications\Providers;

use SudwestFryslan\OpenGovernmentPublications\Container;

abstract class ServiceProvider implements ServiceProviderInterface
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    abstract public function register();
}
