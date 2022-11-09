<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Composer\Composer;
use Composer\Config;
use Composer\Downloader\DownloadManager;
use Composer\IO\IOInterface;
use Composer\Installer\InstallationManager;
use Composer\Package\RootPackage;
use Composer\Repository\RepositoryManager;

/**
 * Тесты
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Вывод
     */
    protected function getMockIO(): IOInterface
    {
        return $this->getMockBuilder(IOInterface::class)->getMock();
    }

    /**
     * Возвращает класс Composer
     */
    protected function getComposer(): Composer
    {
        $composer = new Composer();
        $composer->setPackage($pkg = new RootPackage('root/pkg', '1.0.0.0', '1.0.0'));

        $composer->setConfig(new Config(false));

        $dm = $this->getMockBuilder(DownloadManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $composer->setDownloadManager($dm);

        $im = $this->getMockBuilder(InstallationManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $composer->setInstallationManager($im);

        $rm = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $composer->setRepositoryManager($rm);

        return $composer;
    }
}
