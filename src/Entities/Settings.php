<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities;

use SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders\StorageProviderInterface;

class Settings extends ArrayValueObject
{
    protected StorageProviderInterface $storage;
    protected string $optionKey = 'open_govpub_settings';

    public function __construct(StorageProviderInterface $storage)
    {
        $this->storage = $storage;
        $this->hydrate($storage->get($this->optionKey, []));
    }

    public function has(string $name): bool
    {
        return $this->hasAttributeValue($name);
    }

    /**
     * @param mixed $default
     */
    public function get(string $name, $default = null)
    {
        return $this->getAttributeValue($name, $default);
    }

    public function isEmpty(string $name): bool
    {
        $value = $this->getValue($name, null);

        return empty($value);
    }

    public function isNotEmpty(string $name): bool
    {
        return ! $this->isEmpty($name);
    }

    public function save(string $name, $value): self
    {
        $this->data[$name] = $value;
        $this->storage->save($this->optionKey, $this->data);

        return $this;
    }

    public function update(string $name, $value): self
    {
        $this->data[$name] = $value;
        $this->storage->update($this->optionKey, $this->data);

        return $this;
    }

    public function delete(string $name): self
    {
        unset($this->data[$name]);
        $this->storage->update($this->optionKey, $this->data);

        return $this;
    }

    public function reset(): bool
    {
        return $this->storage->delete($this->optionKey);
    }

    public function refresh(array $data): self
    {
        $this->hydrate($data);

        return $this;
    }
}
