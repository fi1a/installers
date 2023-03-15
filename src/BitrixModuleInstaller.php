<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use CModule;

/**
 * Установщик модулей 1С-Битрикс
 *
 * @codeCoverageIgnore
 */
class BitrixModuleInstaller extends AbstractBitrixInstaller
{
    /**
     * @var bool
     */
    protected static $bitrixIncluded = false;

    /**
     * @inheritDoc
     */
    protected function getDefaultPath(): string
    {
        return '{{bitrix_dir}}/modules/{{vendor}}.{{name}}';
    }

    /**
     * @inheritDoc
     */
    public function install(LibraryInterface $library): bool
    {
        $this->includeBitrix();
        $return = true;

        $moduleId = $this->getModuleId();

        /**
         * @var CModule|false $module
         */
        $module = CModule::CreateModuleObject($moduleId);
        if ($module && !$module->IsInstalled()) {
            /**
             * @var \Bitrix\Main\DB\Connection $connection
             */
            $connection = Application::getConnection();
            /** @psalm-suppress MixedArgument */
            if (
                strtolower($connection->getType()) === 'mysql'
                && defined('MYSQL_TABLE_TYPE')
                && strlen(MYSQL_TABLE_TYPE) > 0
            ) {
                try {
                    $connection->queryExecute('SET storage_engine = "' . MYSQL_TABLE_TYPE . '"');
                } catch (\Throwable $exception) {
                    $connection->queryExecute('SET default_storage_engine = "' . MYSQL_TABLE_TYPE . '"');
                }
            }
            foreach (GetModuleEvents('main', 'OnModuleInstalled', true) as $event) {
                assert(is_array($event));
                ExecuteModuleEventEx($event, [$moduleId, true]);
            }
            /**
             * @psalm-suppress MixedMethodCall
             */
            $return = $module->DoInstall() !== false;
            $return = $return && $library->install();
        }

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function afterInstallCode(): void
    {
        $this->includeBitrix();
        Option::set('fi1a.installers', $this->getModuleId(), 'Y');
    }

    /**
     * @inheritDoc
     */
    public function uninstall(LibraryInterface $library): bool
    {
        $this->includeBitrix();
        $return = true;

        $moduleId = $this->getModuleId();

        /**
         * @var CModule|false $module
         */
        $module = CModule::CreateModuleObject($moduleId);
        if ($module && $module->IsInstalled()) {
            foreach (GetModuleEvents('main', 'OnModuleInstalled', true) as $event) {
                assert(is_array($event));
                ExecuteModuleEventEx($event, [$moduleId, false]);
            }
            $return = $module->DoUninstall() !== false;
            $return = $return && $library->uninstall();
        }

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function afterRemoveCode(): void
    {
        $this->includeBitrix();

        Option::delete('fi1a.installers', [
            'name' => $this->getModuleId(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function update(LibraryInterface $library): bool
    {
        $this->includeBitrix();
        /**
         * @var CModule|false $module
         */
        $module = CModule::CreateModuleObject($this->getModuleId());
        if ($module && $module->IsInstalled()) {
            return $library->update();
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function updateVersion(UpdateVersionInterface $updater): bool
    {
        $this->includeBitrix();
        /**
         * @var CModule|false $module
         */
        $module = CModule::CreateModuleObject($this->getModuleId());
        if ($module && $module->IsInstalled()) {
            return $updater->update();
        }

        return true;
    }

    /**
     * Возвращает идентификатор модуля
     */
    private function getModuleId(): string
    {
        [$vendor, $name] = Helper::getVendorAndName($this->package->getPrettyName());

        return $vendor . '.' . $name;
    }

    /**
     * Подключить битрикс
     */
    private function includeBitrix(): void
    {
        if (static::$bitrixIncluded) {
            return;
        }

        $bitrixDir = $this->getBitrixDir();
        if (
            !(
                strpos($bitrixDir, '/') === 0
                || substr($bitrixDir, 1, 1) === ':'
                || strpos($bitrixDir, '\\\\') === 0
            )
        ) {
            $bitrixDir = getcwd() . '/' . $bitrixDir;
        }
        if (!is_dir($bitrixDir) || !is_file($bitrixDir . '/modules/main/include/prolog_before.php')) {
            throw new \LogicException(sprintf('1С-Битрикс по пути "%s" не найден', $bitrixDir));
        }

        $_SERVER['DOCUMENT_ROOT'] = realpath($bitrixDir . '/..');

        defined('NO_KEEP_STATISTIC') || define('NO_KEEP_STATISTIC', true);
        defined('NOT_CHECK_PERMISSIONS') || define('NOT_CHECK_PERMISSIONS', true);
        defined('BX_WITH_ON_AFTER_EPILOG') || define('BX_WITH_ON_AFTER_EPILOG', true);
        defined('BX_NO_ACCELERATOR_RESET') || define('BX_NO_ACCELERATOR_RESET', true);

        /**
         * @psalm-suppress UnresolvableInclude
         */
        require_once $bitrixDir . '/modules/main/include/prolog_before.php';

        static::$bitrixIncluded = true;
    }
}
