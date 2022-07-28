<?php

use SudwestFryslan\OpenGovernmentPublications\Container;

function ogp_container(?string $abstract = null)
{
    $container = $GLOBALS['ogpcontainer'] ?? new Container();

    return empty($abstract) ? $container : $container->get($abstract);
}
