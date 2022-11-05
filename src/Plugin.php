<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Plugin
 */
class Plugin implements PluginInterface
{
    /**
     * @var Installer|null
     */
    private $installer;

    /**
     * @inheritDoc
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->installer = new Installer($io, $composer);
        $composer->getInstallationManager()->addInstaller($this->installer);
    }

    /**
     * @inheritDoc
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
        if ($this->installer) {
            $composer->getInstallationManager()->removeInstaller($this->installer);
        }
    }

    /**
     * @inheritDoc
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}
