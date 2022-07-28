<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders;

class OptionStorage implements StorageProviderInterface
{
    public function get(string $name, $default = null)
    {
        return get_option($name, $default);
    }

    public function has(string $name): bool
    {
        return get_option($name, null) !== null;
    }

    public function update(string $name, $value): bool
    {
        return (bool) update_option($name, $value);
    }

    public function save(string $name, $value): bool
    {
        return $this->update($name, $value);
    }

    public function delete(string $name): bool
    {
        return (bool) delete_option($name);
    }
}
