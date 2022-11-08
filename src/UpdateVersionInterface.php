<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;

/**
 * Обновление версии
 */
interface UpdateVersionInterface
{
    /**
     * Конструктор
     */
    public function __construct(OutputInterface $output, InputInterface $stream);

    /**
     * Обновление
     */
    public function update(): bool;
}
