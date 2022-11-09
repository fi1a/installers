<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers\Fixtures;

use Fi1a\Installers\AbstractPackageInstaller;
use Fi1a\Installers\LibraryInterface;
use Fi1a\Installers\UpdateVersionInterface;

/**
 * Установщик
 */
class FixtureModuleInstaller extends AbstractPackageInstaller
{
    /**
     * @inheritDoc
     */
    protected function getDefaultPath(): string
    {
        return '{{bitrix_dir}}/modules/{{vendor}}.{{name}}';
    }

    /**
     * @inheritDoc
     */
    public function install(LibraryInterface $library): bool
    {
        return $library->install();
    }

    /**
     * @inheritDoc
     */
    public function uninstall(LibraryInterface $library): bool
    {
        return $library->uninstall();
    }

    /**
     * @inheritDoc
     */
    public function update(LibraryInterface $library): bool
    {
        return $library->update();
    }

    /**
     * @inheritDoc
     */
    public function updateVersion(UpdateVersionInterface $updater): bool
    {
        return true;
    }

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
