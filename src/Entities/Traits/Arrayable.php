<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities\Traits;

trait Arrayable
{
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return $this->hasAttributeValue($offset);
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->getValue($offset);
    }

    public function toArray(): array
    {
        return (array) $this->data;
    }
}
