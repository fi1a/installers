<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aTestmodule\Versions\Version0_0_1;

use Fi1a\Installers\AbstractUpdateVersion;

/**
 * Обновление версии 0.0.1
 */
class UpdateVersion extends AbstractUpdateVersion
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
