<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

/**
 * Установ
 */
class Installer extends LibraryInstaller
{
    /**
     * @var string[]
     */
    private $supportedTypes = [
        'bitrix-d7-module',
    ];

    /**
     * @inheritDoc
     */
    public function supports(string $packageType): bool
    {
        return in_array($packageType, $this->supportedTypes);
    }

    /**
     * @inheritDoc
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package): void
    {
        echo 'install!!!';
    }

    /**
     * @inheritDoc
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package): void
    {
        echo 'uninstall!!!';
    }

    /**
     * @inheritDoc
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ): void {
        echo 'update!!!';
    }
}
