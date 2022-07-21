<?php

use SudwestFryslan\OpenGovernmentPublications\Container;
use SudwestFryslan\OpenGovernmentPublications\AssetLoader;

return [
    'plugin.name'       => 'Open Government Publications',
    'plugin.slug'       => 'open-government-publications',
    'plugin.version'    => '1.0.0',
    'plugin.file'       => dirname(__DIR__) . '/open-government-publications.php',
    'plugin.path'       => dirname(__DIR__),
    'plugin.url'        => plugins_url(basename(dirname(__DIR__))),
    'theme.path'        => fn() => defined('STYLESHEETPATH') ? STYLESHEETPATH : TEMPLATEPATH,

    'user.loggedin'     => fn() => is_user_logged_in(),
    'user.current'      => fn() => wp_get_current_user(),

    AssetLoader::class  => function (Container $container) {
        return new AssetLoader(
            $container->get('plugin.path'),
            $container->get('plugin.url')
        );
    },
];
