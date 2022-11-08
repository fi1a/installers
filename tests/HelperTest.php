<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Fi1a\Installers\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Вспомогательный методы
 */
class HelperTest extends TestCase
{
    /**
     * Возвращает вендора и название пакета
     */
    public function testGetVendorAndName(): void
    {
        $this->assertEquals(['fi1a', 'module-name'], Helper::getVendorAndName('fi1a/module-name'));
        $this->assertEquals(['', 'module-name'], Helper::getVendorAndName('module-name'));
    }

    /**
     * Преобразует строку из ("string_helper" или "string.helper" или "string-helper") в "StringHelper"
     */
    public function testClassify(): void
    {
        $this->assertEquals('StringHelper', Helper::classify('string_helper'));
    }
}
