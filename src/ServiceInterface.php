<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\StreamInput;

/**
 * Интерфейс сервиса
 */
interface ServiceInterface
{
    /**
     * Конструктор
     */
    public function __construct(ConsoleOutputInterface $output, StreamInput $stream, PartialComposer $composer);

    /**
     * Возвращает путь куда будет установлен пакет
     */
    public function getInstallPath(PackageInterface $package): string;

    /**
     * Установка пакета
     */
    public function install(PackageInterface $package): void;

    /**
     * После установки кода пакета
     */
    public function afterInstallCode(PackageInterface $package): void;

    /**
     * Удалить пакет
     */
    public function uninstall(PackageInterface $package): void;

    /**
     * После удаления кода пакета
     */
    public function afterRemoveCode(PackageInterface $package): void;

    /**
     * Обновление пакета
     */
    public function update(PackageInterface $initial, PackageInterface $target): void;

    /**
     * Поддерживается ли тип пакет
     */
    public function supports(string $packageType): bool;
}
