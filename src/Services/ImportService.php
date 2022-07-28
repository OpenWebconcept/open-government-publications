<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Services;

use SudwestFryslan\OpenGovernmentPublications\Support\StorageProviders\StorageProviderInterface;

class ImportService
{
    protected int $lockTime = 3000;
    protected StorageProviderInterface $storage;

    public function __construct(StorageProviderInterface $storage)
    {
        $this->storage = $storage;
    }

    public function isImportLocked(): bool
    {
        return (bool) $this->storage->get('govpub_import_locked', false);
    }

    public function lockImport(?int $duration = null): bool
    {
        return $this->storage->save('govpub_import_locked', true, $duration ?: $this->lockTime);
    }

    public function unlockImport(): bool
    {
        return $this->storage->delete('govpub_import_locked');
    }

    public function isImportCheckLocked(): bool
    {
        return $this->storage->get('govpub_import_check_locked', false);
    }

    public function lockImportCheck(?int $duration = null): bool
    {
        return $this->storage->save('govpub_import_check_locked', true, $duration ?: $this->lockTime);
    }

    public function unlockImportCheck(): bool
    {
        return $this->storage->delete('govpub_import_check_locked');
    }
}
