<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\PartialComposer;
use Fi1a\Format\Formatter;
use InvalidArgumentException;

/**
 * Абстрактный класс типов установщиков библиотек
 */
abstract class AbstractLibraryInstaller implements LibraryInstallerInterface
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
     * @var IOInterface
     */
    protected $io;

    /**
     * Возвращает путь по умолчанию
     */
    abstract protected function getDefaultPath(): string;

    /**
     * @inheritDoc
     */
    public function __construct(PackageInterface $package, PartialComposer $composer, IOInterface $io)
    {
        $this->package = $package;
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @inheritDoc
     */
    public function getInstallPath(): string
    {
        $prettyName = $this->package->getPrettyName();
        if (strpos($prettyName, '/') !== false) {
            [$vendor, $name] = explode('/', $prettyName);
        } else {
            $vendor = '';
            $name = $prettyName;
        }

        $path = $this->getCustomPath($prettyName, $this->package->getType());
        if ($path === false) {
            $path = $this->getDefaultPath();
        }
        if (!$path) {
            throw new InvalidArgumentException(
                sprintf('Не найден путь для установки библиотеки %s', $this->package->getType())
            );
        }

        return Formatter::format(
            $path,
            $this->getPathVariables(['vendor' => $vendor, 'name' => $name,])
        );
    }

    /**
     * Возвращает путь до установки библиотеки
     *
     * @return false|string
     */
    protected function getCustomPath(string $name, string $type)
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
