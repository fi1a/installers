<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\InputInterface;

/**
 * Интерфейс типов установщиков пакета
 */
interface PackageInstallerInterface
{
    /**
     * Конструктор
     */
    public function __construct(
        PackageInterface $package,
        PartialComposer $composer,
        ConsoleOutputInterface $output,
        InputInterface $stream
    );

    /**
     * Возвращает путь установки пакета
     */
    public function getInstallPath(): string;

    /**
     * Установить пакет
     */
    public function install(LibraryInterface $library): bool;

    /**
     * После установки кода пакета
     */
    public function afterInstallCode(): void;

    /**
     * Удалить пакет
     */
    public function uninstall(LibraryInterface $library): bool;

    /**
     * После удаления кода пакета
     */
    public function afterRemoveCode(): void;

    /**
     * Обновить пакет
     */
    public function update(LibraryInterface $library): bool;

    /**
     * Обновить версию
     */
    public function updateVersion(UpdateVersionInterface $updater): bool;
}
