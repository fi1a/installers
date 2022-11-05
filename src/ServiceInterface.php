<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Сервис
 */
interface ServiceInterface
{
    /**
     * Установка пакета
     */
    public function install(): bool;

    /**
     * Удаление пакета
     */
    public function uninstall(): bool;

    /**
     * Обновление пакета
     */
    public function update(): bool;
}
