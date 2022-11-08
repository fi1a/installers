<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Сравнивает версии
 */
class CompareVersion
{
    /**
     * Равны
     */
    public static function isEqual(VersionInterface $current, VersionInterface $updated): bool
    {
        return $current->getPretty() === $updated->getPretty();
    }

    /**
     * Меньше
     */
    public static function isLess(VersionInterface $current, VersionInterface $updated): bool
    {
        if ($current->getMajor() < $updated->getMajor()) {
            return true;
        }
        if ($current->getMajor() === $updated->getMajor() && $current->getMinor() < $updated->getMinor()) {
            return true;
        }

        return $current->getMajor() === $updated->getMajor()
            && $current->getMinor() === $updated->getMinor()
            && $current->getBuild() < $updated->getBuild();
    }
}
