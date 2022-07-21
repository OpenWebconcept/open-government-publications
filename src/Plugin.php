<?php

namespace SudwestFryslan\OpenGovernmentPublications;

class Plugin
{
    protected Container $container;

    public function __construct(Container $container = null)
    {
        $this->container = $container ?: new Container();
        // And this is were the magic happens ( ͡° ͜ʖ ͡°)
        $this->container->set(Container::class, fn($container) => $container);

        $config = array_merge(
            require dirname(__DIR__) . '/config/container.php',
            require dirname(__DIR__) . '/config/services.config.php',
        );

        foreach ($config as $abstract => $factory) {
            $this->container->set($abstract, $factory);
        }

        $GLOBALS['ogpcontainer'] = $this->container;
    }

    public function boot()
    {
        $this->container->get(RestRouteProvider::class)->register();
        $this->container->get(Init::class)->register();
        $this->container->get(Import::class)->register();
        $this->container->get(PostType::class)->register();
        $this->container->get(Settings::class)->register();
        $this->container->get(AdminMenu::class)->register();
        $this->container->get(MetaBoxes::class)->register();

        $this->loadTextDomain();

        register_activation_hook($this->container->get('plugin.file'), [$this, 'activation']);

        register_deactivation_hook($this->container->get('plugin.file'), [$this, 'deactivation']);
    }

    protected function activation()
    {
        $this->container->get(Cronjobs::class)->schedule();
        $this->container->get(Init::class)->importOrganizations();
    }

    public function deactivation()
    {
        // Do something on deactivation
    }

    protected function loadTextDomain()
    {
        load_plugin_textdomain(
            'open-govpub',
            false,
            basename($this->container->get('plugin.path')) . '/languages'
        );
    }
}
