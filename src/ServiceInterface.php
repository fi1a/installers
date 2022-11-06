<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Package\PackageInterface;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\InputInterface;

/**
 * Интерфейс сервиса
 */
interface ServiceInterface
{
    /**
     * Конструктор
     */
    public function __construct(
        PackageInterface $package,
        LibraryInstallerInterface $installer,
        ConsoleOutputInterface $output,
        InputInterface $stream
    );

    /**
     * Возвращает путь куда будет установлена библиотека
     */
    public function getInstallPath(): string;

    /**
     * Установка библиотеки
     */
    public function install(): void;
}
