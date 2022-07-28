<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders;

class TransientStorage implements StorageProviderInterface
{
    public function get(string $name, $default = null)
    {
        $value = get_transient($name);

        return $value === false ? $default : $value;
    }

    public function has(string $name): bool
    {
        return get_transient($name) !== false;
    }

    public function update(string $name, $value, int $expiration = 0): bool
    {
        return (bool) set_transient($name, $value, $expiration);
    }

    public function save(string $name, $value, int $expiration = 0): bool
    {
        return $this->update($name, $value, $expiration);
    }

    public function delete(string $name): bool
    {
        return (bool) delete_transient($name);
    }
}
