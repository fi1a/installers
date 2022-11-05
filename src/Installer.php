<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use React\Promise\PromiseInterface;

/**
 * Composer LibraryInstaller
 */
class Installer extends LibraryInstaller
{
    /**
     * @var LibraryInstallerInterface|null
     */
    private $installer;

    /**
     * @var string[]
     */
    private $supportedTypes = [
        'bitrix-d7-module' => BitrixModuleInstaller::class,
    ];

    /**
     * @inheritDoc
     */
    public function supports(string $packageType): bool
    {
        return array_key_exists($packageType, $this->supportedTypes);
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        /**
         * @psalm-suppress PossiblyNullReference
         */
        $path = $this->installer->getInstallPath();
        if (!$this->filesystem->isAbsolutePath($path)) {
            $path = getcwd() . '/' . $path;
        }

        return $path;
    }

    /**
     * @inheritDoc
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $class = $this->supportedTypes[mb_strtolower($package->getType())];
        /**
         * @var LibraryInstallerInterface $instance
         * @psalm-suppress InvalidStringClass
         */
        $instance = new $class($package, $this->composer, $this->io);
        assert($instance instanceof LibraryInstallerInterface);
        $this->installer = $instance;

        $outputStatus = function (): void {
            echo 'install!!!';
        };

        $promise = parent::install($repo, $package);

        return $promise instanceof PromiseInterface ? $promise->then($outputStatus) : null;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $outputStatus = function (): void {
            echo 'uninstall!!!';
        };
        $promise = parent::uninstall($repo, $package);

        return $promise instanceof PromiseInterface ? $promise->then($outputStatus) : null;
    }

    /**
     * @inheritDoc
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        $outputStatus = function (): void {
            echo 'update!!!';
        };

        $promise = parent::update($repo, $initial, $target);

        return $promise instanceof PromiseInterface ? $promise->then($outputStatus) : null;
    }
}
