<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\PartialComposer;

/**
 * Интерфейс сервиса
 */
interface ServiceInterface
{
    /**
     * Конструктор
     */
    public function __construct(IOInterface $io, PartialComposer $composer);

    /**
     * Возвращает путь куда будет установлен пакет
     */
    public function getInstallPath(PackageInterface $package): string;

    /**
     * Установка пакета
     */
    public function install(PackageInterface $package): void;

    /**
     * Удалить пакет
     */
    public function uninstall(PackageInterface $package): void;

    /**
     * Поддерживается ли тип пакет
     */
    public function supports(string $packageType): bool;
}
