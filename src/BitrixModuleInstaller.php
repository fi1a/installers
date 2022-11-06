<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Установщик модулей 1С-Битрикс
 */
class BitrixModuleInstaller extends AbstractBitrixInstaller
{
    /**
     * @inheritDoc
     */
    protected function getDefaultPath(): string
    {
        return '{{bitrix_dir}}/modules/{{vendor}}.{{name}}';
    }
}
