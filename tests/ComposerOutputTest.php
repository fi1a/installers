<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Fi1a\Console\IO\Formatter;
use Fi1a\Console\IO\OutputInterface;
use Fi1a\Installers\ComposerOutput;

/**
 * Вывод через класс IOInterface
 */
class ComposerOutputTest extends TestCase
{
    /**
     * Тестирование вывода
     */
    public function testWrite(): void
    {
        $composerOutput = new ComposerOutput($this->getMockIO(), new Formatter(), true);
        $this->assertTrue($composerOutput->writeln('test'));
    }

    /**
     * Тестирование вывода
     */
    public function testVerbose(): void
    {
        $composerOutput = new ComposerOutput($this->getMockIO(), new Formatter(), true);
        $composerOutput->setVerbose(OutputInterface::VERBOSE_NONE);
        $this->assertTrue(
            $composerOutput->writeln('test', [], null, OutputInterface::VERBOSE_DEBUG)
        );
    }
}
