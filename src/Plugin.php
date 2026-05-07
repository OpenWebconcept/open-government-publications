<?php

namespace SudwestFryslan\OpenGovernmentPublications;

use Throwable;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Plugin
{
    protected Container $container;

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?: new Container();
        // And this is where the magic happens ( ͡° ͜ʖ ͡°)
        $this->container->set(Container::class, fn($container) => $container);

        $config = array_merge(
            require dirname(__DIR__) . '/config/container.php',
            require dirname(__DIR__) . '/config/config.php',
        );

        foreach ($config as $abstract => $factory) {
            $this->container->set($abstract, $factory);
        }

        $GLOBALS['ogpcontainer'] = $this->container;
    }

    public function boot(): void
    {
        $this->container->get(Init::class)->register();

        $this->loadTextDomain();

        $this->registerServiceProviders();

        register_activation_hook($this->container->get('plugin.file'), [$this, 'activation']);

        register_deactivation_hook($this->container->get('plugin.file'), [$this, 'deactivation']);

        $this->checkForUpdate();
    }

    public function activation(): void
    {
        $this->container->get(Services\EventService::class)->schedule();
        $this->container->get(Init::class)->importOrganizations();
    }

    public function deactivation(): void
    {
        $this->container->get(Services\EventService::class)->unschedule();
    }

    protected function registerServiceProviders(): void
    {
        $this->container->get(Providers\RestRouteProvider::class)->register();
        $this->container->get(Providers\ImportProvider::class)->register();
        $this->container->get(Providers\PostTypeProvider::class)->register();
        $this->container->get(Providers\SettingsProvider::class)->register();
        $this->container->get(Providers\AdminMenuProvider::class)->register();
        $this->container->get(Providers\MetaboxProvider::class)->register();
    }

    protected function loadTextDomain(): void
    {
        load_plugin_textdomain(
            'open-govpub',
            false,
            basename($this->container->get('plugin.path')) . '/languages'
        );
    }

    protected function checkForUpdate(): void
    {
        try {
            $updater = PucFactory::buildUpdateChecker(
                'https://github.com/OpenWebconcept/open-government-publications/',
                $this->container->get('plugin.file'),
                'open-government-publications'
            );

            $updater->setBranch('master');

            $vcsApi = $updater->getVcsApi();
            if ($vcsApi instanceof \YahnisElsts\PluginUpdateChecker\v5p6\Vcs\GitHubApi) {
                $vcsApi->enableReleaseAssets();
            }
        } catch (Throwable $e) {
            error_log($e->getMessage());
        }
    }
}
