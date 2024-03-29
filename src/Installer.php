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
use Fi1a\Console\IO\ConsoleInput;
use Fi1a\Console\IO\Formatter;
use Fi1a\Console\IO\Style\TrueColorStyle;
use React\Promise\PromiseInterface;

/**
 * Composer LibraryInstaller
 *
 * @codeCoverageIgnore
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
        $output = new ComposerOutput($io, new Formatter(TrueColorStyle::class), true);
        $stream = new ConsoleInput();
        $this->service = new Service($output, $stream, $composer);
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
            $service->afterInstallCode($package);
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

        $then = function () use ($service, $package): void {
            $service->afterRemoveCode($package);
        };

        $promise = parent::uninstall($repo, $package);

        return $promise instanceof PromiseInterface ? $promise->then($then) : null;
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
