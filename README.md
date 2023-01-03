# Установка и обновление пакетов через composer для фреймворков

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
![Coverage Status][badge-coverage]
[![Total Downloads][badge-downloads]][downloads]
[![Support mail][badge-mail]][mail]

Эта библиотека осуществляет установку, обновление и удаление пакетов на основе типа через composer для фреймворков.
Имеется возможность настроить путь установки для каждого пакета.
Помимо размещения по нужному пути осуществляется установка пакета в фреймворке, если устанавлевыемый пакет поддерживает это.

## Поддерживаемые типы пакетов

| Framework | Types              |
|-----------|--------------------|
| Bitrix    | `bitrix-d7-module` |

## Пример composer.json файла

В файле composer.json необходимо указать тип пакета "type": "bitrix-d7-module" и подключить пакет «require»: { "fi1a/installers": "^2.0" }.

```json
{
 "name": "foo/bar",
 "type": "bitrix-d7-module",
 "require": {
  "fi1a/installers": "^2.0"
 }
}
```

Это установит ваш пакет в папку с модулями 1С-Битрикс, когда пользователь запустит установку.

## Пользовательские пути установки

Доступные переменные для использования в путях: {{vendor}}, {{name}}.

Вы можете указать путь для установки в `composer.json` для пакетов:

```json
{
 "extra": {
  "installer-paths": {
    "bitrix/modules/{{vendor}}.{{name}}": ["foo/bar", "baz/qux"]
  }
 }
}
```

Вы можете указать путь для установки в `composer.json` для определенных типов пакетов:

```json
{
 "extra": {
  "installer-paths": {
    "bitrix/modules/{{vendor}}.{{name}}": ["type:bitrix-d7-module"]
  }
 }
}
```

Вы можете указать путь для установки в `composer.json` для определенного vendor:

```json
{
 "extra": {
  "installer-paths": {
    "bitrix/modules/{{vendor}}.{{name}}": ["vendor:foo"]
  }
 }
}
```

## Установка через composer модуля 1С-Битрикс (тип пакета bitrix-d7-module)

Для установки модуля 1С-Битрикс (тип пакета ```bitrix-d7-module```) необходимо указать путь до папки с 1С-Битрикс в 
вашем `composer.json` файле проекта, после чего выполнить установку пакета через ```composer require```, 
предварительно выполнив ```composer require fi1a/installers```.

```json
{
 "extra": {
  "bitrix-dir": "../bitrix"
 }
}
```

```shell
composer require fi1a/installers
composer require foo/bar
```

## Поддержка установки и удаления пакета

Класс библиотеки используется для определения возможности установки или удаления пакета (методы ```canInstall``` и ```canUninstall```).
Также содержит методы вызываемые при установке, удалении или обновлении пакета (методы ```install```, ```uninstall``` и ```update```). 

Класс библиотеки должен располагаться по пути `installers/Library.php`, иметь название
```Fi1a\Installers\{{Vendor}}{{Name}}\Library``` и реализовывать интерфейс ```Fi1a\Installers\LibraryInterface```.

| Метод                   | Описание                                |
|-------------------------|-----------------------------------------|
| ```canInstall```        | Можно установить пакет или нет          |
| ```canUninstall```      | Можно удалить пакет или нет             |
| ```install```           | Устанавливает пакет                     |
| ```uninstall```         | Удаляет пакет                           |
| ```update```            | Обновляет пакет                         |
| ```getCurrentVersion``` | Возвращает текущую версию пакета        |
| ```getUpdateVersion```  | Возвращает версию для обновления пакета |

Пример класса библиотеки для типа пакета ```bitrix-d7-module```:

```php
<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aBitrixd7moduleinstallerdemo;

use Bitrix\Main\Config\Option;
use CModule;
use ErrorException;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;
use Fi1a\Installers\AbstractLibrary;
use Fi1a\Installers\Version;
use Fi1a\Installers\VersionInterface;

/**
 * Библиотека
 */
class Library extends AbstractLibrary
{
    public const MODULE_ID = 'fi1a.bitrixd7moduleinstallerdemo';

    /**
     * @inheritDoc
     */
    public function __construct(OutputInterface $output, InputInterface $stream)
    {
        parent::__construct($output, $stream);
        $this->includeBitrix();
    }

    /**
     * @inheritDoc
     */
    public function canInstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canUninstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function install(): bool
    {
        $this->output->writeln('<notice>Library->install</notice>');

        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): bool
    {
        $this->output->writeln('<notice>Library->uninstall</notice>');

        return true;
    }

    /**
     * @inheritDoc
     */
    public function update(): bool
    {
        $this->output->writeln('<notice>Library->update</notice>');

        /**
         * @var \fi1a_bitrixd7moduleinstallerdemo|false $module
         * @psalm-suppress UnusedVariable
         */
        $module = CModule::CreateModuleObject(self::MODULE_ID);
        if ($module) {
            // @codingStandardsIgnoreStart
            Option::set(self::MODULE_ID, 'version', (string) $module->MODULE_VERSION);
            // @codingStandardsIgnoreEnd
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentVersion(): VersionInterface
    {
        [$major, $minor, $build] = explode(
            '.',
            (string) Option::get(self::MODULE_ID, 'version', '1.0.0')
        );

        return new Version((int) $major, (int) $minor, (int) $build);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateVersion(): VersionInterface
    {
        /**
         * @var \fi1a_bitrixd7moduleinstallerdemo|false $module
         */
        $module = CModule::CreateModuleObject(self::MODULE_ID);
        if (!$module) {
            throw new ErrorException(sprintf('Модуль "%s" не найден', self::MODULE_ID));
        }
        // @codingStandardsIgnoreStart
        [$major, $minor, $build] = explode(
            '.',
            (string) $module->MODULE_VERSION
        );
        // @codingStandardsIgnoreEnd

        return new Version((int) $major, (int) $minor, (int) $build);
    }

    /**
     * Подключить битрикс
     */
    private function includeBitrix(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../..');

        defined('NO_KEEP_STATISTIC') || define('NO_KEEP_STATISTIC', true);
        defined('NOT_CHECK_PERMISSIONS') || define('NOT_CHECK_PERMISSIONS', true);
        defined('BX_WITH_ON_AFTER_EPILOG') || define('BX_WITH_ON_AFTER_EPILOG', true);
        defined('BX_NO_ACCELERATOR_RESET') || define('BX_NO_ACCELERATOR_RESET', true);

        /**
         * @psalm-suppress UnresolvableInclude
         */
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    }
}

```

## Поддержка обновления пакета

При обновлении пакета осуществляется поиск файлов версий обновлений и последовательный запуск их.

Файлы с версиями для обновления расположены по пути `installers/versions` (0.1.0, 1.2.0, ...) вашего пакета.

Класс должен иметь имя ```Fi1a\Installers\{{Vendor}}{{Name}}\Versions\Version{{Major}}_{{Minor}}_{{Build}}\UpdateVersion```,
реализовывать интерфейс ```Fi1a\Installers\UpdateVersionInterface``` и располагаться по пути `installers/versions/{{Major}}.{{Minor}}.{{Build}}/UpdateVersion.php`

Пример:

```php
<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aBitrixd7moduleinstallerdemo\Versions\Version1_1_0;

use Fi1a\Installers\AbstractUpdateVersion;

/**
 * Обновление версии 1.1.0
 */
class UpdateVersion extends AbstractUpdateVersion
{
    /**
     * @inheritDoc
     */
    public function update(): bool
    {
        require __DIR__ . '/updater.php';
        $this->output->writeln('UpdateVersion->update 1.1.0');

        return true;
    }
}

```

Пример расположен по пути `installers/versions/1.1.0/UpdateVersion.php`

[badge-release]: https://img.shields.io/packagist/v/fi1a/installers?label=release
[badge-license]: https://img.shields.io/github/license/fi1a/installers?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/fi1a/installers?style=flat-square
[badge-coverage]: https://img.shields.io/badge/coverage-100%25-green
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/installers.svg?style=flat-square&colorB=mediumvioletred
[badge-mail]: https://img.shields.io/badge/mail-support%40fi1a.ru-brightgreen

[packagist]: https://packagist.org/packages/fi1a/installers
[license]: https://github.com/fi1a/installers/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/fi1a/installers
[mail]: mailto:support@fi1a.ru