<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Абстрактный установщик 1С-Битрикс
 */
abstract class AbstractBitrixInstaller extends AbstractPackageInstaller
{
    /**
     * @inheritDoc
     */
    protected function getPathVariables(array $vars): array
    {
        $vars['bitrix_dir'] = $this->getBitrixDir();

        return parent::getPathVariables($vars);
    }

    /**
     * Возвращает директорию до битрикса
     */
    protected function getBitrixDir(): string
    {
        $bitrixDir = 'bitrix';

        $extra = $this->composer->getPackage()->getExtra();

        if (isset($extra['bitrix-dir']) && $extra['bitrix-dir']) {
            $bitrixDir = (string) $extra['bitrix-dir'];
        }

        return $bitrixDir;
    }
}
