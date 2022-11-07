<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Format\Formatter;
use InvalidArgumentException;

/**
 * Абстрактный класс типов установщиков пакетов
 */
abstract class AbstractPackageInstaller implements PackageInstallerInterface
{
    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @var PartialComposer
     */
    protected $composer;

    /**
     * @var ConsoleOutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $stream;

    /**
     * Возвращает путь по умолчанию
     */
    abstract protected function getDefaultPath(): string;

    /**
     * @inheritDoc
     */
    public function __construct(
        PackageInterface $package,
        PartialComposer $composer,
        ConsoleOutputInterface $output,
        InputInterface $stream
    ) {
        $this->package = $package;
        $this->composer = $composer;
        $this->output = $output;
        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    public function getInstallPath(): string
    {
        $prettyName = $this->package->getPrettyName();
        [$vendor, $name] = Helper::getVendorAndName($prettyName);

        $path = $this->getCustomPath($prettyName, $vendor, $this->package->getType());
        if ($path === false) {
            $path = $this->getDefaultPath();
        }
        if (!$path) {
            throw new InvalidArgumentException(
                sprintf('Не найден путь для установки пакета %s', $this->package->getType())
            );
        }

        return Formatter::format(
            $path,
            $this->getPathVariables(['vendor' => $vendor, 'name' => $name,])
        );
    }

    /**
     * Возвращает путь до установки пакета
     *
     * @return false|string
     */
    protected function getCustomPath(string $name, string $vendor, string $type)
    {
        $type = mb_strtolower($type);
        /**
         * @var string[][] $extra
         */
        $extra = $this->composer->getPackage()->getExtra();
        if (!isset($extra['installer-paths'])) {
            return false;
        }
        foreach ($extra['installer-paths'] as $path => $names) {
            $names = (array) $names;
            if (
                in_array($name, $names)
                || in_array('type:' . $type, $names)
                || in_array('vendor:' . $vendor, $names)
            ) {
                return (string) $path;
            }
        }

        return false;
    }

    /**
     * Возвращает значения для замены
     *
     * @param string[] $vars
     *
     * @return string[]
     */
    protected function getPathVariables(array $vars): array
    {
        return $vars;
    }
}
