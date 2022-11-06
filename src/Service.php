<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Package\PackageInterface;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\InteractiveInput;

/**
 * Сервис
 */
class Service implements ServiceInterface
{
    /**
     * @var LibraryInstallerInterface
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
     * @var PackageInterface
     */
    private $package;

    /**
     * @inheritDoc
     */
    public function __construct(
        PackageInterface $package,
        LibraryInstallerInterface $installer,
        ConsoleOutputInterface $output,
        InputInterface $stream
    ) {
        $this->package = $package;
        $this->installer = $installer;
        $this->output = $output;
        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    public function install(): void
    {
        $library = $this->getLibrary();
        if (!$library) {
            return;
        }
        if (!$library->canInstall()) {
            return;
        }
        $interactive = new InteractiveInput($this->output, $this->stream);

        $interactive
            ->addValue('install')
            ->description('<question>Установить пакет "' . $this->package->getPrettyName() . '" (y/n)?</question>')
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

        if ($this->installer->install($library)) {
            $this->output->writeln(
                '<success>Пакет "{{name}}" успешно установлен</success>',
                ['name' => $this->package->getPrettyName()]
            );

            return;
        }
        $this->output->writeln(
            '<error>Не удалось установить пакет "{{name}}"</error>',
            ['name' => $this->package->getPrettyName()]
        );
    }

    /**
     * Возвращает класс библиотеки
     *
     * @return LibraryInterface|false
     */
    private function getLibrary()
    {
        $prettyName = $this->package->getPrettyName();
        $classify = Helper::classify($prettyName);
        $path = $this->getInstallPath() . '/installers/' . $classify . '.php';
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
            throw new \LogicException('Класс библиотеки должен реализовывать интерфейс ' . LibraryInterface::class);
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(): string
    {
        $path = $this->installer->getInstallPath();
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
}
