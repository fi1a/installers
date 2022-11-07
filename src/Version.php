<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Версия пакета
 */
class Version implements VersionInterface
{
    /**
     * @var int
     */
    private $major;

    /**
     * @var int
     */
    private $minor;

    /**
     * @var int
     */
    private $build;

    /**
     * @inheritDoc
     */
    public function __construct(int $major, int $minor, int $build)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->build = $build;
    }

    /**
     * @inheritDoc
     */
    public function getMajor(): int
    {
        return $this->major;
    }

    /**
     * @inheritDoc
     */
    public function getMinor(): int
    {
        return $this->minor;
    }

    /**
     * @inheritDoc
     */
    public function getBuild(): int
    {
        return $this->build;
    }

    /**
     * @inheritDoc
     */
    public function getPretty(): string
    {
        return $this->getMajor() . '.' . $this->getMinor() . '.' . $this->getBuild();
    }
}
