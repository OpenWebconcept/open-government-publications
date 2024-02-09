<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Support;

use InvalidArgumentException;

abstract class Template
{
    protected array $data = [];
    protected PathResolver $pathResolver;

    public function __construct(PathResolver $resolver, array $args = [])
    {
        $this->pathResolver = $resolver;
        $this->mergeData($args);
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function render(array $args = []): string
    {
        $this->mergeData($args);

        $this->preRender();

        $path = $this->ensureValidPath($this->getTemplatePath());

        ob_start();
        extract($this->getData());
        include $this->preInclude($path);

        return $this->afterRender(ob_get_clean());
    }

    public function output(array $args = []): void
    {
        echo $this->render($args);
    }

    public function mergeData(array $data): Template
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function setData(string $name, $value): Template
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function unsetData(string $name): void
    {
        unset($this->data[$name]);
    }

    public function hasData(): bool
    {
        return ! empty($this->data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function snippet(string $name): string
    {
        $path = $this->ensureValidPath($this->pathResolver->view($name));

        ob_start();
        extract($this->getData());
        include $path;

        return ob_get_clean();
    }

    protected function getDataByName(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    protected function preRender(): void
    {
        // overwrite...
    }

    protected function preInclude(string $path): string
    {
        return $path; // overwrite...
    }

    protected function afterRender(string $rendered): string
    {
        return $rendered; // overwrite...
    }

    abstract protected function getTemplatePath(): string;

    protected function ensureValidPath(string $path): string
    {
        if (! file_exists($path) || ! is_readable($path)) {
            throw new InvalidArgumentException(
                sprintf("Non-existing or unreadable template %s", basename($path))
            );
        }

        return $path;
    }
}
