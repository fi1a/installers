<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;

/**
 * Интерфейс библиотеки
 */
interface LibraryInterface
{
    /**
     * Конструктор
     */
    public function __construct(OutputInterface $output, InputInterface $stream);

    /**
     * Можно установить библиотеку или нет
     */
    public function canInstall(): bool;

    /**
     * Можно удалить библиотеку или нет
     */
    public function canUninstall(): bool;

    /**
     * Устанавливает библиотеку
     */
    public function install(): bool;

    /**
     * Удаляет библиотеку
     */
    public function uninstall(): bool;
}
