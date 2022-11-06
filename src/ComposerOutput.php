<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Fi1a\Console\IO\AbstractOutput;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\FormatterInterface;
use Fi1a\Console\IO\OutputInterface;

/**
 * Вывод через класс IOInterface
 */
class ComposerOutput extends AbstractOutput implements ConsoleOutputInterface
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var OutputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $errorOutput;

    /**
     * @inheritDoc
     */
    public function __construct(IOInterface $io, FormatterInterface $formatter, bool $decorated = false)
    {
        $this->io = $io;
        $this->setErrorOutput(new ComposerOutputError($io, $formatter, $decorated));
        parent::__construct($formatter, $decorated);
    }

    /**
     * @inheritDoc
     */
    protected function doWrite(string $message, bool $newLine): bool
    {
        $this->io->writeRaw($message, $newLine);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setErrorOutput(OutputInterface $output): bool
    {
        $this->errorOutput = $output;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getErrorOutput(): OutputInterface
    {
        return $this->errorOutput;
    }

    /**
     * @inheritDoc
     */
    public function setDecorated(bool $decorated): bool
    {
        parent::setDecorated($decorated);

        return $this->getErrorOutput()->setDecorated($decorated);
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface $formatter): bool
    {
        parent::setFormatter($formatter);

        return $this->getErrorOutput()->setFormatter($formatter);
    }

    /**
     * @inheritDoc
     */
    public function setVerbose(int $verbose): bool
    {
        parent::setVerbose($verbose);

        return $this->getErrorOutput()->setVerbose($verbose);
    }
}
