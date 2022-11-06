<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Абстрактный установщик 1С-Битрикс
 */
abstract class AbstractBitrixInstaller extends AbstractLibraryInstaller
{
    /**
     * @inheritDoc
     */
    protected function getPathVariables(array $vars): array
    {
        $vars['bitrix_dir'] = 'bitrix';

        $extra = $this->composer->getPackage()->getExtra();

        if (isset($extra['bitrix-dir']) && $extra['bitrix-dir']) {
            $vars['bitrix_dir'] = (string) $extra['bitrix-dir'];
        }

        return $vars;
    }
}
