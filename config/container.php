<?php

use SudwestFryslan\OpenGovernmentPublications\Container;
use SudwestFryslan\OpenGovernmentPublications\AssetLoader;
use SudwestFryslan\OpenGovernmentPublications\Services\ImportService;
use SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders\OptionStorage;
use SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders\TransientStorage;
use SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders\StorageProviderInterface;

return [
    'plugin.name'       => 'Open Government Publications',
    'plugin.slug'       => 'open-government-publications',
    'plugin.version'    => '2.0.2',
    'plugin.file'       => dirname(__DIR__) . '/open-government-publications.php',
    'plugin.path'       => dirname(__DIR__),
    'plugin.url'        => plugins_url(basename(dirname(__DIR__))),
    'theme.path'        => fn() => (defined('STYLESHEETPATH') ? STYLESHEETPATH : TEMPLATEPATH),

    'user.loggedin'     => fn() => is_user_logged_in(),
    'user.current'      => fn() => wp_get_current_user(),

    AssetLoader::class  => function (Container $container) {
        return new AssetLoader(
            $container->get('plugin.path'),
            $container->get('plugin.url')
        );
    },
    StorageProviderInterface::class => fn() => new OptionStorage(),
    ImportService::class => function (Container $container): ImportService {
        return new ImportService($container->get(TransientStorage::class));
    },
];
