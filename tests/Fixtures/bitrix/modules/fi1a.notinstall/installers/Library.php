<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aNotinstall;

use Fi1a\Installers\AbstractLibrary;
use Fi1a\Installers\Version;
use Fi1a\Installers\VersionInterface;

/**
 * Библиотека
 */
class Library extends AbstractLibrary
{
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
