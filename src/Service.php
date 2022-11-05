<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Сервис
 */
class Service implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function update(): bool
    {
        return true;
    }
}
