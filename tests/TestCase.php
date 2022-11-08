<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Composer\IO\IOInterface;

/**
 * Тесты
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Вывод
     */
    protected function getMockIO(): IOInterface
    {
        return $this->getMockBuilder(IOInterface::class)->getMock();
    }
}
