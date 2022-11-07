<?php

declare(strict_types=1);

namespace Fi1a\Installers;

/**
 * Вспомогательный класс
 */
class Helper
{
    /**
     * Возвращает вендора и название пакета
     *
     * @return string[]
     */
    public static function getVendorAndName(string $prettyName): array
    {
        if (strpos($prettyName, '/') !== false) {
            return explode('/', $prettyName);
        }

        return ['', $prettyName];
    }

    /**
     * Преобразует строку из ("string_helper" или "string.helper" или "string-helper") в "StringHelper"
     */
    public static function classify(string $value, string $delimiter = ''): string
    {
        return trim(preg_replace_callback('/(^|_|\.|\-|\/)([a-z ]+)/im', function ($matches) use ($delimiter) {
            return ucfirst(mb_strtolower($matches[2])) . $delimiter;
        }, $value . ' '), ' ' . $delimiter);
    }
}
