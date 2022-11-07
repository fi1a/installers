<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Интерфейс версии пакета
 */
interface VersionInterface
{
    /**
     * Конструктор
     */
    public function __construct(int $major, int $minor, int $build);

    /**
     * Старший номер версии
     */
    public function getMajor(): int;

    /**
     * Младший номер версии
     */
    public function getMinor(): int;

    /**
     * Номер билда
     */
    public function getBuild(): int;
}
