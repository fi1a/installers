<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aTestmodule\Versions\Version2_1_0;

/**
 * Обновление версии 2.1.0
 */
class UpdateVersion
{
    /**
     * @inheritDoc
     */
    public function update(): bool
    {
        require __DIR__ . '/updater.php';

        return true;
    }
}
