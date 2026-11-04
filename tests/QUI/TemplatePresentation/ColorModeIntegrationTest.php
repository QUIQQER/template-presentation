<?php

declare(strict_types=1);

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI\Projects\Colors;
use QUI\Projects\Manager;
use QUI\Projects\ProjectTestHelper;
use QUI\TemplatePresentation\ColorMode;
use QUI\TemplatePresentation\Utils;

/** Persistence tests must run with Core's isolated bootstrap. */
final class ColorModeIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(ProjectTestHelper::class) || !getenv('QUIQQER_CORE_TEST_RUNTIME')) {
            self::markTestSkipped('Use the Core isolated test bootstrap for project persistence coverage.');
        }
    }

    protected function tearDown(): void
    {
        if (class_exists(ProjectTestHelper::class) && getenv('QUIQQER_CORE_TEST_RUNTIME')) {
            ProjectTestHelper::cleanup();
        }
    }

    public function testNormalProjectSetupUsesSystemAndAdaptiveColorDefaults(): void
    {
        $Project = ProjectTestHelper::getProject();
        $Project->setTemplate('quiqqer/template-presentation');
        ProjectTestHelper::runAsSystemUser(static fn () => $Project->setup());
        Manager::getConfig()->reload();
        $Project->refresh();

        self::assertSame('system', ColorMode::getSettings($Project)['defaultMode']);

        $expectedColors = [
            'colorMainContentFont' => 'light-dark(#3b3b3a, #e5e7eb)',
            'colorFooterBackground' => 'light-dark(#eeeeee, #1c2128)',
            'navBarBgColor' => 'light-dark(#2d4d88, #1c2128)',
            'mobileMenuBackground' => 'light-dark(#252122, #1c2128)',
            'bricks.bgColor' => 'light-dark(#f5f5f5, #252c35)'
        ];

        foreach ($expectedColors as $key => $expected) {
            self::assertSame($expected, Colors::toCss($Project->getConfig('templatePresentation.settings.' . $key)));
        }

        self::assertSame('#ffffff', $Project->getConfig('templatePresentation.settings.navBarLinkColor'));
        self::assertSame('#ffffff', $Project->getConfig('templatePresentation.settings.mobileFontColor'));
    }

    public function testEmptyColorSettingsRenderAdaptiveDefaultsThroughCoreColors(): void
    {
        $Project = ProjectTestHelper::getProject();
        $Config = Manager::getConfig();

        foreach (['colorMainContentFont', 'colorFooterBackground', 'navBarBgColor', 'mobileMenuBackground'] as $key) {
            $Config->setValue($Project->getName(), 'templatePresentation.settings.' . $key, '');
        }

        $Config->save();
        $Project->refresh();
        $Reflection = new \ReflectionClass(Utils::class);
        $Reflection->getProperty('Project')->setValue(null, $Project);
        $Reflection->getProperty('Site')->setValue(
            null,
            ProjectTestHelper::runAsSystemUser(static fn () => $Project->firstChild())
        );
        $variables = $Reflection->getMethod('getCssVariables')->invoke(null);
        self::assertSame('light-dark(#3b3b3a, #e5e7eb)', $variables['bodyColor']);
        self::assertSame('light-dark(#eeeeee, #1c2128)', $variables['footerBgColor']);
        self::assertSame('light-dark(#2d4d88, #1c2128)', $variables['navBgColor']);
        self::assertSame('light-dark(#252122, #1c2128)', $variables['navMobileBgColor']);
        self::assertSame('light-dark(#fafafa, #1c2128)', $variables['navSubMenuBgColor']);
    }

    public function testNormalSetupPreservesSavedModesAndConcreteColors(): void
    {
        $Project = ProjectTestHelper::getProject();
        $Project->setTemplate('quiqqer/template-presentation');
        $Config = Manager::getConfig();
        $colors = [
            'colorMainContentFont' => '#3b3b3a',
            'colorFooterBackground' => '#eeeeee',
            'navBarBgColor' => '#2d4d88',
            'mobileMenuBackground' => '#252122',
            'bricks.bgColor' => '#f5f5f5',
            'colorMain' => '#abcdef'
        ];

        foreach ($colors as $key => $color) {
            $Config->setValue($Project->getName(), 'templatePresentation.settings.' . $key, $color);
        }

        foreach (['light', 'dark', 'system'] as $mode) {
            $Config->setValue($Project->getName(), 'templatePresentation.settings.colorMode', $mode);
            $Config->save();
            ProjectTestHelper::runAsSystemUser(static fn () => $Project->setup());
            $Config->reload();
            $Project->refresh();
            self::assertSame($mode, ColorMode::getSettings($Project)['defaultMode']);

            foreach ($colors as $key => $color) {
                self::assertSame($color, $Project->getConfig('templatePresentation.settings.' . $key));
            }
        }
    }
}
