<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Fi1a\Console\IO\Formatter;
use Fi1a\Installers\ComposerOutput;

/**
 * Вывод ошибок через класс IOInterface
 */
class ComposerOutputErrorTest extends TestCase
{
    /**
     * Тестирование вывода
     */
    public function testWrite(): void
    {
        $composerOutput = new ComposerOutput($this->getMockIO(), new Formatter(), true);
        $this->assertTrue($composerOutput->getErrorOutput()->writeln('test'));
    }
}
