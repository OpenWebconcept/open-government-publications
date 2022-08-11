<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Entities\Traits;

trait Loopable
{
    public function rewind(): void
    {
        reset($this->data);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    public function next(): void
    {
        next($this->data);
    }

    public function valid(): bool
    {
        return key($this->data) !== null;
    }
}
