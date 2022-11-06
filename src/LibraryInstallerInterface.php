<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;

/**
 * Интерфейс типов установщиков библиотек
 */
interface LibraryInstallerInterface
{
    /**
     * Конструктор
     */
    public function __construct(
        PackageInterface $package,
        PartialComposer $composer,
        OutputInterface $output,
        InputInterface $stream
    );

    /**
     * Возвращает путь установки пакета
     */
    public function getInstallPath(): string;
}
