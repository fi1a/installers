<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\PartialComposer;

/**
 * Интерфейс типов установщиков библиотек
 */
interface LibraryInstallerInterface
{
    /**
     * Конструктор
     */
    public function __construct(PackageInterface $package, PartialComposer $composer, IOInterface $io);

    /**
     * Возвращает путь установки пакета
     */
    public function getInstallPath(): string;
}
