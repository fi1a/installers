<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aLibraryexception;

use Fi1a\Installers\Version;
use Fi1a\Installers\VersionInterface;

/**
 * Библиотека
 */
class Library
{
    /**
     * @inheritDoc
     */
    public function canInstall(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canUninstall(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function update(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentVersion(): VersionInterface
    {
        return new Version(1, 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateVersion(): VersionInterface
    {
        return new Version(1, 1, 0);
    }
}
