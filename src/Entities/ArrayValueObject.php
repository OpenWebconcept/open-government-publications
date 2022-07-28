<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities;

use Iterator;
use ArrayAccess;
use InvalidArgumentException;

class ArrayValueObject implements ArrayAccess, Iterator
{
    use Traits\Arrayable;
    use Traits\Loopable;

    protected array $data;

    public function __construct($itemData)
    {
        $this->hydrate($itemData);
    }

    public function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * @param mixed $default
     */
    public function getValue(string $name, $default = null)
    {
        if ($this->hasMutator($name)) {
            return $this->getMutatedValue($name, $this->getAttributeValue($name, $default));
        }

        return $this->getAttributeValue($name, $default);
    }

    public function getAttributeValue(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function hasAttributeValue(string $name): bool
    {
        return isset($this->data[$name]);
    }

    protected function hasMutator(string $name): bool
    {
        return method_exists($this, $name);
    }

    protected function getMutatedValue(string $name, $value)
    {
        return $this->{$name}($value);
    }

    /**
     * @param  array|object $data
     */
    protected function hydrate($data): void
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (! is_array($data)) {
            throw new InvalidArgumentException(
                sprintf("Unable to hydrate %s: invalid data type", get_called_class())
            );
        }

        $this->data = $data;
    }
}
