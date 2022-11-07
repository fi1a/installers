<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Fi1a\Console\IO\ConsoleInput;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\Formatter;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\InteractiveInput;
use Fi1a\Console\IO\Style\TrueColorStyle;
use Fi1a\Format\Formatter as FormatFormatter;

/**
 * Сервис
 */
class Service implements ServiceInterface
{
    /**
     * @var string[]
     */
    private $supportedTypes = [
        'bitrix-d7-module' => BitrixModuleInstaller::class,
    ];

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $stream;

    /**
     * @var PartialComposer
     */
    private $composer;

    /**
     * @inheritDoc
     */
    public function __construct(IOInterface $io, PartialComposer $composer)
    {
        $this->output = new ComposerOutput($io, new Formatter(TrueColorStyle::class), true);
        $this->stream = new ConsoleInput();
        $this->composer = $composer;
    }

    /**
     * @inheritDoc
     */
    public function install(PackageInterface $package): void
    {
        $library = $this->getLibrary($package);
        if (!$library) {
            return;
        }
        if (!$library->canInstall()) {
            return;
        }
        $interactive = new InteractiveInput($this->output, $this->stream);

        $interactive
            ->addValue('install')
            ->description(
                FormatFormatter::format(
                    'Установить пакет <color=green>"{{name}}"</> (<color=yellow>y/n</>)?',
                    [
                        'name' => $package->getPrettyName(),
                    ]
                )
            )
            ->default(false)
            ->validation()
            ->allOf()
            ->boolean();

        $interactive->read();

        $installValue = $interactive->getValue('install');
        $isInstall = $installValue && in_array(
            mb_strtolower((string) $installValue->getValue()),
            ['y', '1', 'true', 'yes']
        );

        if (!$isInstall) {
            return;
        }

        if ($this->getInstaller($package)->install($library)) {
            $this->output->writeln(
                '<color=green>Пакет "{{name}}" успешно установлен</>',
                ['name' => $package->getPrettyName()]
            );

            return;
        }
        $this->output->writeln(
            '<error>Не удалось установить пакет "{{name}}"</error>',
            ['name' => $package->getPrettyName()]
        );
    }

    /**
     * @inheritDoc
     */
    public function uninstall(PackageInterface $package): void
    {
        $library = $this->getLibrary($package);
        if (!$library) {
            return;
        }
        if (!$library->canUninstall()) {
            return;
        }

        $interactive = new InteractiveInput($this->output, $this->stream);

        $interactive
            ->addValue('uninstall')
            ->description(
                FormatFormatter::format(
                    'Удалить пакет <color=green>"{{name}}"</> (<color=yellow>y/n</>)?',
                    [
                        'name' => $package->getPrettyName(),
                    ]
                )
            )
            ->default(false)
            ->validation()
            ->allOf()
            ->boolean();

        $interactive->read();

        $installValue = $interactive->getValue('uninstall');
        $isUninstall = $installValue && in_array(
            mb_strtolower((string) $installValue->getValue()),
            ['y', '1', 'true', 'yes']
        );

        if (!$isUninstall) {
            return;
        }

        if ($this->getInstaller($package)->uninstall($library)) {
            $this->output->writeln(
                '<color=green>Пакет "{{name}}" успешно удален</>',
                ['name' => $package->getPrettyName()]
            );

            return;
        }
        $this->output->writeln(
            '<error>Не удалось удалить пакет "{{name}}"</error>',
            ['name' => $package->getPrettyName()]
        );
    }

    /**
     * @inheritDoc
     */
    public function update(PackageInterface $initial, PackageInterface $target): void
    {
        $library = $this->getLibrary($target);
        if (!$library) {
            return;
        }

        echo $library->getCurrentVersion()->getPretty();
        echo $library->getUpdateVersion()->getPretty();

        //$this->getInstaller($package)->install($library);
    }

    /**
     * Возвращает класс пакета
     *
     * @return LibraryInterface|false
     */
    private function getLibrary(PackageInterface $package)
    {
        $prettyName = $package->getPrettyName();
        $classify = Helper::classify($prettyName);
        $path = $this->getInstallPath($package) . '/installers/' . $classify . '.php';
        if (!is_file($path)) {
            return false;
        }
        $class = '\\Fi1a\\Installers\\' . $classify;
        include_once $path;
        if (!class_exists($class)) {
            return false;
        }
        /**
         * @var LibraryInterface $instance
         * @psalm-suppress MixedMethodCall
         */
        $instance = new $class($this->output, $this->stream);
        if (!is_subclass_of($instance, LibraryInterface::class)) {
            throw new \LogicException('Класс пакета должен реализовывать интерфейс ' . LibraryInterface::class);
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package): string
    {
        $path = $this->getInstaller($package)->getInstallPath();
        if (
            !(
            strpos($path, '/') === 0
            || substr($path, 1, 1) === ':'
            || strpos($path, '\\\\') === 0
            )
        ) {
            $path = getcwd() . '/' . $path;
        }

        return $path;
    }

    /**
     * Возвращает установщик
     */
    protected function getInstaller(PackageInterface $package): PackageInstallerInterface
    {
        $class = $this->supportedTypes[mb_strtolower($package->getType())];
        /**
         * @var PackageInstallerInterface $instance
         * @psalm-suppress InvalidStringClass
         */
        $instance = new $class($package, $this->composer, $this->output, $this->stream);
        assert($instance instanceof PackageInstallerInterface);

        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $packageType): bool
    {
        return array_key_exists($packageType, $this->supportedTypes);
    }
}
