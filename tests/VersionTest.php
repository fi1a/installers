<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Fi1a\Installers\Version;
use PHPUnit\Framework\TestCase;

/**
 * Версия пакета
 */
class VersionTest extends TestCase
{
    /**
     * Версия пакета
     */
    public function testVersion(): void
    {
        $version = new Version(1, 2, 0);
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(0, $version->getBuild());
        $this->assertEquals('1.2.0', $version->getPretty());
    }
}
