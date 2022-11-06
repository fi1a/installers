<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Интерфейс библиотеки
 */
interface LibraryInterface
{
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
