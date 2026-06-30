<?php

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI\Package\Package;
use QUI\TemplatePresentation\EventHandler;
use Smarty;

class EventHandlerTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tmpDir = sys_get_temp_dir() . '/quiqqer-template-presentation-tests-' . md5((string)mt_rand());
        mkdir($this->tmpDir . '/bin/css', 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $cssFile = $this->tmpDir . '/bin/css/project-settings.css';

        if (is_file($cssFile)) {
            @unlink($cssFile);
        }

        @rmdir($this->tmpDir . '/bin/css');
        @rmdir($this->tmpDir . '/bin');
        @rmdir($this->tmpDir);
    }

    public function testOnPackageSetupReturnsEarlyForOtherPackages(): void
    {
        $Package = $this->createMock(Package::class);
        $Package->method('getName')->willReturn('quiqqer/other-package');

        EventHandler::onPackageSetup($Package);

        $this->assertFileDoesNotExist($this->tmpDir . '/bin/css/project-settings.css');
    }

    public function testOnPackageSetupRemovesLegacyCssFile(): void
    {
        $cssFile = $this->tmpDir . '/bin/css/project-settings.css';
        file_put_contents($cssFile, ':root {}');

        $Package = $this->createMock(Package::class);
        $Package->method('getName')->willReturn('quiqqer/template-presentation');
        $Package->method('getDir')->willReturn($this->tmpDir . '/');

        EventHandler::onPackageSetup($Package);

        $this->assertFileDoesNotExist($cssFile);
    }

    public function testOnSmartyInitRegistersUtilsClass(): void
    {
        $Smarty = new Smarty();

        EventHandler::onSmartyInit($Smarty);

        $this->assertArrayHasKey('QUI\\TemplatePresentation\\Utils', $Smarty->registered_classes);
        $this->assertSame(
            '\\QUI\\TemplatePresentation\\Utils',
            $Smarty->registered_classes['QUI\\TemplatePresentation\\Utils']
        );
    }

    public function testOnSmartyInitDoesNotOverrideExistingRegistration(): void
    {
        $Smarty = new Smarty();
        $Smarty->registered_classes['QUI\\TemplatePresentation\\Utils'] = 'Custom\\Utils';

        EventHandler::onSmartyInit($Smarty);

        $this->assertSame('Custom\\Utils', $Smarty->registered_classes['QUI\\TemplatePresentation\\Utils']);
    }
}
