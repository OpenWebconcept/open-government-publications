<?php

use SudwestFryslan\OpenGovernmentPublications\Container;
use SudwestFryslan\OpenGovernmentPublications\AssetLoader;

return [
    'plugin.name'       => 'Open Government Publications',
    'plugin.slug'       => 'open-government-publications',
    'plugin.version'    => '1.0.0',
    'plugin.file'       => dirname(__DIR__) . '/index.php',
    'plugin.path'       => dirname(__DIR__),
    'plugin.url'        => plugins_url(basename(dirname(__DIR__))),
    'theme.path'        => fn() => defined('STYLESHEETPATH') ? STYLESHEETPATH : TEMPLATEPATH,

    'user.loggedin'     => fn() => is_user_logged_in(),
    'user.current'      => fn() => wp_get_current_user(),

    // Legacy variables
    'OPEN_GOVPUB_VERSION'   => '1.0.0',
    'OPEN_GOVPUB_DEBUG'     => OPEN_GOVPUB_DEBUG,
    'OPEN_GOVPUB_FILE'      => OPEN_GOVPUB_FILE,
    'OPEN_GOVPUB_DIR'       => OPEN_GOVPUB_DIR,
    'OPEN_GOVPUB_BASENAME'  => OPEN_GOVPUB_BASENAME,
    'OPEN_GOVPUB_URL'       => OPEN_GOVPUB_URL,

    AssetLoader::class  => function (Container $container) {
        return new AssetLoader(
            $container->get('OPEN_GOVPUB_DIR'),
            $container->get('OPEN_GOVPUB_URL')
        );
    },
];
