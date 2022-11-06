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
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\Formatter;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\Style\TrueColorStyle;
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
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $stream;

    /**
     * @var string[]
     */
    private $supportedTypes = [
        'bitrix-d7-module' => BitrixModuleInstaller::class,
    ];

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
        $this->output = new ComposerOutput($this->io, new Formatter(TrueColorStyle::class), true);
        $this->stream = new ConsoleInput();
    }

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
        $service = new Service(
            $package,
            $this->getInstallerInstance($package),
            $this->output,
            $this->stream
        );

        return $service->getInstallPath();
    }

    /**
     * @inheritDoc
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $service = new Service(
            $package,
            $this->getInstallerInstance($package),
            $this->output,
            $this->stream
        );

        $promise = parent::install($repo, $package);

        return $promise instanceof PromiseInterface ? $promise->then([$service, 'install']) : null;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->getInstallerInstance($package);

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
        $this->getInstallerInstance($initial);

        $outputStatus = function (): void {
            echo 'update!!!';
        };

        $promise = parent::update($repo, $initial, $target);

        return $promise instanceof PromiseInterface ? $promise->then($outputStatus) : null;
    }

    /**
     * Возвращает установщик
     */
    protected function getInstallerInstance(PackageInterface $package): LibraryInstallerInterface
    {
        if ($this->installer) {
            return $this->installer;
        }

        $class = $this->supportedTypes[mb_strtolower($package->getType())];
        /**
         * @var LibraryInstallerInterface $instance
         * @psalm-suppress InvalidStringClass
         */
        $instance = new $class($package, $this->composer, $this->output, $this->stream);
        assert($instance instanceof LibraryInstallerInterface);

        return $this->installer = $instance;
    }
}
