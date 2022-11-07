<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;

/**
 * Интерфейс пакета
 */
interface LibraryInterface
{
    /**
     * Конструктор
     */
    public function __construct(OutputInterface $output, InputInterface $stream);

    /**
     * Можно установить пакет или нет
     */
    public function canInstall(): bool;

    /**
     * Можно удалить пакет или нет
     */
    public function canUninstall(): bool;

    /**
     * Устанавливает пакет
     */
    public function install(): bool;

    /**
     * Удаляет пакет
     */
    public function uninstall(): bool;
}
