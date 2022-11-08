<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Fi1a\Installers\CompareVersion;
use Fi1a\Installers\Version;
use PHPUnit\Framework\TestCase;

/**
 * Сравнивает версии
 */
class CompareVersionTest extends TestCase
{
    /**
     * Версии равны
     */
    public function testIsEqual(): void
    {
        $version1 = new Version(1, 2, 0);
        $version2 = new Version(1, 2, 0);
        $this->assertTrue(CompareVersion::isEqual($version1, $version2));
        $version3 = new Version(1, 2, 1);
        $this->assertFalse(CompareVersion::isEqual($version1, $version3));
    }

    /**
     * Версии равны
     */
    public function testIsLess(): void
    {
        $version1 = new Version(1, 2, 0);
        $version2 = new Version(1, 2, 0);
        $this->assertFalse(CompareVersion::isLess($version1, $version2));
        $version3 = new Version(1, 2, 1);
        $this->assertTrue(CompareVersion::isLess($version1, $version3));
        $this->assertFalse(CompareVersion::isLess($version2, $version1));
        $version4 = new Version(1, 0, 0);
        $version5 = new Version(2, 0, 0);
        $this->assertTrue(CompareVersion::isLess($version4, $version5));
        $version6 = new Version(1, 1, 0);
        $version7 = new Version(1, 2, 0);
        $this->assertTrue(CompareVersion::isLess($version6, $version7));
    }
}
