<?php

declare(strict_types=1);

namespace Fi1a\Unit\Installers;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\Formatter;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\Stream;
use Fi1a\Console\IO\StreamInput;
use Fi1a\Console\IO\StreamInterface;
use Fi1a\Console\IO\Style\TrueColorStyle;
use Fi1a\Installers\ComposerOutput;
use Fi1a\Installers\Fi1aTestmodule\Library;
use Fi1a\Installers\Service;
use Fi1a\Installers\Version;
use Fi1a\Unit\Installers\Fixtures\FixtureModuleInstaller;
use InvalidArgumentException;
use LogicException;

/**
 * Сервис
 */
class ServiceTest extends TestCase
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var StreamInterface
     */
    private $stream;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->composer = $this->getComposer();
        $this->composer->getPackage()->setExtra([
            'bitrix-dir' => 'tests/Fixtures/bitrix',
        ]);
        $this->io = $this->getMockIO();
        $this->output = new ComposerOutput($this->io, new Formatter(TrueColorStyle::class), true);
        $this->stream = new Stream('php://memory');
        $this->input = new StreamInput($this->stream);
        include_once __DIR__ . '/Fixtures/bitrix/modules/fi1a.testmodule/installers/Library.php';
    }

    /**
     * Данные для теста testSupports
     *
     * @return mixed[][]
     */
    public function dataProviderSupports(): array
    {
        return [
            // 0
            ['bitrix-d7-module', true],
            // 1
            ['unknown', false],
        ];
    }

    /**
     * Возвращает путь куда будет установлен пакет
     *
     * @dataProvider dataProviderSupports
     */
    public function testSupports(string $type, bool $result): void
    {
        $service = new Service($this->output, $this->input, $this->composer);
        $this->assertEquals($result, $service->supports($type));
    }

    /**
     * Данные для теста testGetInstallPath
     *
     * @return string[][]
     */
    public function dataProviderGetInstallPath(): array
    {
        return [
            // 0
            [
                'bitrix-d7-module', 'tests/Fixtures/bitrix/modules/fi1a.testmodule', 'fi1a/testmodule',
            ],
        ];
    }

    /**
     * Возвращает путь куда будет установлен пакет
     *
     * @dataProvider dataProviderGetInstallPath
     */
    public function testGetInstallPath(string $type, string $path, string $name, string $version = '1.0.0'): void
    {
        $package = new Package($name, $version, $version);
        $package->setType($type);
        $service = new Service($this->output, $this->input, $this->composer);
        $this->assertEquals(getcwd() . '/' . $path, $service->getInstallPath($package));
    }

    /**
     * Возвращает путь куда будет установлен пакет
     */
    public function testGetInstallPathVendor(): void
    {
        $this->composer->getPackage()->setExtra([
            'installer-paths' => [
                'tests/Fixtures/bitrix/modules/fi1a.testmodule' => ['vendor:fi1a'],
            ],
        ]);
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');
        $service = new Service($this->output, $this->input, $this->composer);
        $this->assertEquals(
            getcwd() . '/tests/Fixtures/bitrix/modules/fi1a.testmodule',
            $service->getInstallPath($package)
        );
    }

    /**
     * Возвращает путь куда будет установлен пакет
     */
    public function testGetInstallPathVendorNotFound(): void
    {
        $this->composer->getPackage()->setExtra([
            'installer-paths' => [
                'tests/Fixtures/bitrix/modules/fi1a.testmodule' => ['vendor:unknown'],
            ],
        ]);
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');
        $service = new Service($this->output, $this->input, $this->composer);
        $this->assertEquals(getcwd() . '/bitrix/modules/fi1a.testmodule', $service->getInstallPath($package));
    }

    /**
     * Возвращает путь куда будет установлен пакет
     */
    public function testGetInstallPathNotFoundPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->composer->getPackage()->setExtra([
            'installer-paths' => [
                false => ['vendor:fi1a'],
            ],
        ]);
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');
        $service = new Service($this->output, $this->input, $this->composer);
        $service->getInstallPath($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testInstall(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $this->stream->write('y');
        $this->stream->seek(0);
        $service->install($package);
    }

    /**
     * Событие после установки кода пакета
     */
    public function testAfterInstallCode(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->afterInstallCode($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testNotInstall(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $this->stream->write('n');
        $this->stream->seek(0);
        $service->install($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testInstallNoLibraryFile(): void
    {
        $package = new Package('fi1a/unknown', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller');

        $service->install($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testInstallLibraryNotInstall(): void
    {
        $package = new Package('fi1a/notinstall', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->install($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testInstallError(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $installer = $this->getMockBuilder(FixtureModuleInstaller::class)
            ->onlyMethods(['install'])
            ->setConstructorArgs([
                $package,
                $this->composer,
                $this->output,
                $this->input,
            ])
            ->getMock();

        $installer->expects($this->atLeastOnce())
            ->method('install')
            ->will($this->returnValue(false));

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue($installer));

        $this->stream->write('y');
        $this->stream->seek(0);
        $service->install($package);
    }

    /**
     * Удалить пакет
     */
    public function testUninstall(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $this->stream->write('y');
        $this->stream->seek(0);
        $service->uninstall($package);
    }

    /**
     * Событие после удаления кода пакета
     */
    public function testAfterRemoveCode(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->afterRemoveCode($package);
    }

    /**
     * Удалить пакет
     */
    public function testNotUninstall(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $this->stream->write('n');
        $this->stream->seek(0);
        $service->uninstall($package);
    }

    /**
     * Удалить пакет
     */
    public function testUninstallNoLibraryFile(): void
    {
        $package = new Package('fi1a/unknown', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller');

        $service->uninstall($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testUninstallLibraryNotUninstall(): void
    {
        $package = new Package('fi1a/notinstall', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $package,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->uninstall($package);
    }

    /**
     * Устанавливает пакет
     */
    public function testUninstallError(): void
    {
        $package = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $package->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $installer = $this->getMockBuilder(FixtureModuleInstaller::class)
            ->onlyMethods(['uninstall'])
            ->setConstructorArgs([
                $package,
                $this->composer,
                $this->output,
                $this->input,
            ])
            ->getMock();

        $installer->expects($this->atLeastOnce())
            ->method('uninstall')
            ->will($this->returnValue(false));

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue($installer));

        $this->stream->write('y');
        $this->stream->seek(0);
        $service->uninstall($package);
    }

    /**
     * Обновление пакета
     */
    public function testUpdate(): void
    {
        $initial = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $target,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateNoLibraryFile(): void
    {
        $initial = new Package('fi1a/unknown', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/unknown', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $target,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateEqualVersions(): void
    {
        $initial = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getLibrary'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $library = $this->getMockBuilder(Library::class)
            ->onlyMethods(['getUpdateVersion'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $library->expects($this->atLeastOnce())
            ->method('getUpdateVersion')
            ->will($this->returnValue(
                new Version(1, 0, 0)
            ));

        $service->expects($this->atLeastOnce())
            ->method('getLibrary')
            ->will($this->returnValue($library));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateLessVersions(): void
    {
        $initial = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getLibrary'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $library = $this->getMockBuilder(Library::class)
            ->onlyMethods(['getUpdateVersion'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $library->expects($this->atLeastOnce())
            ->method('getUpdateVersion')
            ->will($this->returnValue(
                new Version(0, 1, 0)
            ));

        $service->expects($this->atLeastOnce())
            ->method('getLibrary')
            ->will($this->returnValue($library));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateNoVersionDir(): void
    {
        $initial = new Package('fi1a/notinstall', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/notinstall', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $target,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateClassException(): void
    {
        $this->expectException(LogicException::class);
        $initial = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/testmodule', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getLibrary', 'getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $library = $this->getMockBuilder(Library::class)
            ->onlyMethods(['getUpdateVersion'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $library->expects($this->atLeastOnce())
            ->method('getUpdateVersion')
            ->will($this->returnValue(
                new Version(2, 1, 0)
            ));

        $service->expects($this->atLeastOnce())
            ->method('getLibrary')
            ->will($this->returnValue($library));

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $target,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateNoLibraryClass(): void
    {
        $initial = new Package('fi1a/nolibraryclass', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/nolibraryclass', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $target,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->update($initial, $target);
    }

    /**
     * Обновить пакет
     */
    public function testUpdateLibraryException(): void
    {
        $this->expectException(LogicException::class);
        $initial = new Package('fi1a/libraryexception', '1.0.0', '1.0.0');
        $initial->setType('bitrix-d7-module');
        $target = new Package('fi1a/libraryexception', '1.0.0', '1.0.0');
        $target->setType('bitrix-d7-module');

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['getInstaller'])
            ->setConstructorArgs([$this->output, $this->input, $this->composer])
            ->getMock();

        $service->expects($this->atLeastOnce())
            ->method('getInstaller')
            ->will($this->returnValue(
                new FixtureModuleInstaller(
                    $target,
                    $this->composer,
                    $this->output,
                    $this->input
                )
            ));

        $service->update($initial, $target);
    }
}
