<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Fi1a\Console\IO\FormatterInterface;

/**
 * Вывод ошибок через класс IOInterface
 */
class ComposerOutputError extends \Fi1a\Console\IO\AbstractOutput
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @inheritDoc
     */
    public function __construct(IOInterface $io, FormatterInterface $formatter, bool $decorated = false)
    {
        $this->io = $io;
        parent::__construct($formatter, $decorated);
    }

    /**
     * @inheritDoc
     */
    protected function doWrite(string $message, bool $newLine): bool
    {
        $this->io->writeErrorRaw($message, $newLine);

        return true;
    }
}
