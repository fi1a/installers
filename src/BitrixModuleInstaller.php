<?php

declare(strict_types=1);

namespace Fi1a\Installers;

use Bitrix\Main\Application;
use CModule;

/**
 * Установщик модулей 1С-Битрикс
 */
class BitrixModuleInstaller extends AbstractBitrixInstaller
{
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
        if (!is_dir($bitrixDir)) {
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

        $return = true;

        $moduleId = $this->getModuleId();

        /**
         * @var CModule $module
         */
        $module = CModule::CreateModuleObject($moduleId);
        if (!$module->IsInstalled()) {
            /**
             * @var \Bitrix\Main\DB\Connection $connection
             */
            $connection = Application::getConnection();
            if (
                strtolower((string) $connection->getType()) === 'mysql'
                && defined('MYSQL_TABLE_TYPE')
                && MYSQL_TABLE_TYPE
            ) {
                $connection->queryExecute('SET storage_engine = "' . MYSQL_TABLE_TYPE . '"');
            }
            foreach (GetModuleEvents('main', 'OnModuleInstalled', true) as $event) {
                assert(is_array($event));
                ExecuteModuleEventEx($event, [$moduleId, true]);
            }
            /**
             * @psalm-suppress MixedMethodCall
             */
            $return = (bool) $module->DoInstall();
            $return = $return && $library->install();
        }

        return $return;
    }

    /**
     * Возвращает идентификатор модуля
     */
    private function getModuleId(): string
    {
        [$vendor, $name] = Helper::getVendorAndName($this->package->getPrettyName());

        return $vendor . '.' . $name;
    }
}
