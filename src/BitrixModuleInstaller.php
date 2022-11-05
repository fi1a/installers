<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Установщик модулей 1С-Битрикс
 */
class BitrixModuleInstaller extends AbstractBitrixInstaller
{
    /**
     * @var string
     */
    protected $defaultPath = 'local/modules/{{vendor}}.{{name}}';
}
