<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;

/**
 * Абстрактный класс библиотеки
 */
abstract class AbstractLibrary implements LibraryInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $stream;

    /**
     * @inheritDoc
     */
    public function __construct(OutputInterface $output, InputInterface $stream)
    {
        $this->output = $output;
        $this->stream = $stream;
    }

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
}
