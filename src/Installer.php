<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Composer\Installer\BinaryInstaller;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use React\Promise\PromiseInterface;

/**
 * Composer LibraryInstaller
 */
class Installer extends LibraryInstaller
{
    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @inheritDoc
     */
    public function __construct(
        IOInterface $io,
        PartialComposer $composer,
        ?string $type = 'library',
        ?Filesystem $filesystem = null,
        ?BinaryInstaller $binaryInstaller = null
    ) {
        parent::__construct($io, $composer, $type, $filesystem, $binaryInstaller);
        $this->service = new Service($io, $composer);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $packageType): bool
    {
        return $this->service->supports($packageType);
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->service->getInstallPath($package);
    }

    /**
     * @inheritDoc
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $service = $this->service;
        $then = function () use ($service, $package): void {
            $service->install($package);
        };

        $promise = parent::install($repo, $package);

        return $promise instanceof PromiseInterface ? $promise->then($then) : null;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $service = $this->service;
        $service->uninstall($package);

        return parent::uninstall($repo, $package);
    }

    /**
     * @inheritDoc
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        $service = $this->service;
        $then = function () use ($service, $initial, $target): void {
            $service->update($initial, $target);
        };

        $promise = parent::update($repo, $initial, $target);

        return $promise instanceof PromiseInterface ? $promise->then($then) : null;
    }
}
