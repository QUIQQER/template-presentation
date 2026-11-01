<?php

declare(strict_types=1);

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI;
use QUI\Projects\Colors;
use QUI\Projects\Manager;
use QUI\Projects\Project;
use QUI\Projects\ProjectTestHelper;
use QUI\TemplatePresentation\EventHandler;
use QUI\TemplatePresentation\Palette;
use QUI\TemplatePresentation\Utils;

/** Run with Core's isolated tests/phpunit-bootstrap.php; never write into a normal installation. */
final class ProjectColorsIntegrationTest extends TestCase
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

    public function testBrickOutputFollowsSavedPaletteChangesWithoutRewritingItsReference(): void
    {
        $Project = ProjectTestHelper::getProject();
        $reference = '{"type":"palette","id":"primary"}';
        $Brick = new QUI\Bricks\Brick(['project' => $Project->getName()]);
        $Brick->setSettings([
            'enableBrickBgColor' => 'enable.useBrickSetting',
            'brickBgColor' => $reference,
            'enableBrickTextColor' => 'enable.useBrickSetting',
            'brickTextColor' => '#fedcba'
        ]);

        foreach (['#123456', '#abcdef'] as $color) {
            self::savePrimary($Project, $color);
            $css = Utils::getBrickBgColorCssVar($Brick);
            self::assertSame('--qui-tpl-brick-backgroundColor: var(--qui-project-color-primary);', $css);
            self::assertSame($color, Colors::getCssVariables($Project)['--qui-project-color-primary']);
            self::assertSame($reference, $Brick->getSetting('brickBgColor'));
            self::assertSame('--qui-tpl-brick-textColor: #fedcba;', Utils::getBrickTextColorCssVar($Brick));
        }
    }

    public function testStandardPaletteAndRenderingUsePackageDefaultIndependentlyOfSavedColors(): void
    {
        $Project = ProjectTestHelper::getProject();
        self::savePrimary($Project, '#abcdef');
        ProjectTestHelper::runAsSystemUser(static function () use ($Project): void {
            Manager::setConfigForProject($Project->getName(), [
                'templatePresentation.settings.colorMain' => '#123456'
            ]);
        });
        Manager::getConfig()->reload();
        Manager::cleanup();
        $Project = ProjectTestHelper::getProject();

        $Dom = new \DOMDocument();
        $Dom->load(dirname(__DIR__, 3) . '/settings.xml');
        $expected = (new \DOMXPath($Dom))->evaluate(
            'string(/quiqqer/project/settings/config/section[@name="templatePresentation.settings"]/conf[@name="colorMain"]/defaultvalue)'
        );
        $palette = (new Palette())->getPalettes($Project)[0]['colors'];
        self::assertSame($expected, $palette['primary']['value']);
        self::assertSame($expected, $palette['link']['light']);
        self::assertSame('#abcdef', Colors::get($Project)['colors']['primary']['value']);

        $Reflection = new \ReflectionClass(Utils::class);
        $Reflection->getProperty('Project')->setValue(null, $Project);
        $Reflection->getProperty('Site')->setValue(
            null,
            ProjectTestHelper::runAsSystemUser(static fn () => $Project->firstChild())
        );
        $variables = $Reflection->getMethod('getCssVariables')->invoke(null);
        self::assertSame('#123456', $variables['colorPrimary']);

        // Verify rendering of an invalid stored value independently of the field control.
        Manager::getConfig()->setValue($Project->getName(), 'templatePresentation.settings.colorMain', 'invalid');
        Manager::cleanup();
        $Reflection->getProperty('Project')->setValue(null, ProjectTestHelper::getProject());
        $variables = $Reflection->getMethod('getCssVariables')->invoke(null);
        self::assertSame($expected, $variables['colorPrimary']);

        Manager::getConfig()->setValue($Project->getName(), 'templatePresentation.settings.colorMain', '');
        Manager::cleanup();
        $Reflection->getProperty('Project')->setValue(null, ProjectTestHelper::getProject());
        $variables = $Reflection->getMethod('getCssVariables')->invoke(null);
        self::assertSame($expected, $variables['colorPrimary']);

        $Project = ProjectTestHelper::getProject();
        $Project->setTemplate('quiqqer/template-presentation');
        $editorSettings = ['cssFiles' => []];
        EventHandler::onEditorGetSettings($Project, $editorSettings);
        $editorCss = base64_decode(substr($editorSettings['cssFiles'][0], strlen('data:text/css;base64,')));
        self::assertStringContainsString('--template-settings__color-primary:' . $expected . ';', $editorCss);
    }

    public function testFrontendTranslatesColorsAndOmitsEmptyDeclarations(): void
    {
        $Project = ProjectTestHelper::getProject();
        ProjectTestHelper::runAsSystemUser(static function () use ($Project): void {
            Manager::setConfigForProject($Project->getName(), [
                'templatePresentation.settings.colorMain' => '{"type":"palette","id":"primary"}',
                'templatePresentation.settings.colorMainContentFont' => '{"type":"color","light":"#1234","dark":"#abcdef80"}',
                'templatePresentation.settings.colorMainContrast' => '#ffffff'
            ]);
        });
        Manager::getConfig()->reload();
        Manager::cleanup();
        $Project = ProjectTestHelper::getProject();
        $Site = ProjectTestHelper::runAsSystemUser(static fn () => $Project->firstChild());
        $Reflection = new \ReflectionClass(Utils::class);
        $Reflection->getProperty('Project')->setValue(null, $Project);
        $Reflection->getProperty('Site')->setValue(null, $Site);
        $variables = $Reflection->getMethod('getCssVariables')->invoke(null);

        $Setting = new QUI\QDOM();
        $Setting->setAttribute('cssVariables', $variables);
        $Template = new QUI\QDOM();
        $Template->setAttribute('TemplateSetting', $Setting);

        require_once OPT_DIR . 'smarty/smarty/libs/bootstrap.php';
        $Engine = new \Smarty();
        $Engine->setCompileDir(VAR_DIR . 'cache/presentation-color-tests');
        $Engine->assign('Template', $Template);
        $output = $Engine->fetch(dirname(__DIR__, 3) . '/template/head/cssVariables.html');

        self::assertStringContainsString('--template-settings__color-primary: var(--qui-project-color-primary);', $output);
        self::assertStringContainsString('--template-settings__body-color: light-dark(#1234, #abcdef80);', $output);
        self::assertStringContainsString('--template-settings__color-primary-contrast: #ffffff;', $output);
        self::assertStringNotContainsString('--template-settings__heading-color:', $output);
        self::assertStringNotContainsString('--qui-project-color-primary:', $output);
    }

    public function testEditorReceivesCurrentPaletteOnBothFreshAndCachedSettings(): void
    {
        $Project = ProjectTestHelper::getProject();
        ProjectTestHelper::runAsSystemUser(static function () use ($Project): void {
            Manager::setConfigForProject($Project->getName(), [
                'templatePresentation.settings.colorMain' => '{"type":"palette","id":"primary"}'
            ]);
        });
        $handler = [EventHandler::class, 'onEditorGetSettings'];
        QUI::getEvents()->addEvent('onEditorGetSettings', $handler);

        try {
            $previousColor = null;

            foreach (['#123456', '#abcdef'] as $color) {
                self::savePrimary($Project, $color);
                $CurrentProject = Manager::getProject($Project->getName());
                $CurrentProject->setTemplate('quiqqer/template-presentation');
                $fresh = QUI\Editor\Manager::getSettings($CurrentProject);
                $cached = QUI\Editor\Manager::getSettings($CurrentProject);
                self::assertSame($fresh, $cached);
                $styles = array_values(array_filter(
                    $cached['cssFiles'],
                    static fn (string $file): bool => str_starts_with($file, 'data:text/css;base64,')
                ));
                self::assertCount(2, $styles);
                $css = base64_decode(substr($styles[0], strlen('data:text/css;base64,')));
                $templateCss = base64_decode(substr($styles[1], strlen('data:text/css;base64,')));
                self::assertStringContainsString('--qui-project-color-primary:' . $color . ';', $css);
                self::assertStringContainsString(
                    '--template-settings__color-primary:var(--qui-project-color-primary);',
                    $templateCss
                );
                self::assertStringNotContainsString('--qui-project-color-primary:', $templateCss);

                if ($previousColor !== null) {
                    self::assertStringNotContainsString($previousColor, $css);
                }

                $previousColor = $color;
            }
        } finally {
            QUI::getEvents()->removeEvent('onEditorGetSettings', $handler);
        }
    }

    private static function savePrimary(Project $Project, string $color): void
    {
        $palette = Colors::get($Project);
        $palette['colors']['primary'] = ['value' => $color];
        ProjectTestHelper::runAsSystemUser(static function () use ($Project, $palette): void {
            Manager::setConfigForProject($Project->getName(), [
                Colors::CONFIG_KEY => json_encode([
                    'palette' => json_encode($palette, JSON_THROW_ON_ERROR)
                ], JSON_THROW_ON_ERROR)
            ]);
        });
        Manager::getConfig()->reload();
        Manager::cleanup();
    }
}
