<?php

declare(strict_types=1);

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI\Projects\Project;
use QUI\TemplatePresentation\ColorMode;

final class ColorModeTest extends TestCase
{
    /** @param array<string, mixed> $config keyed by setting name without prefix */
    private function createProject(array $config, string $name = 'demo'): Project
    {
        $Project = $this->createMock(Project::class);
        $Project->method('getName')->willReturn($name);
        $Project->method('getConfig')->willReturnCallback(
            static fn (string $key): mixed => $config[substr($key, strlen('templatePresentation.settings.'))] ?? false
        );

        return $Project;
    }

    public function testMissingAndInvalidSettingsUseSystemWithoutVisitorSwitch(): void
    {
        foreach ([false, '', 'invalid', ['dark']] as $value) {
            $Project = $this->createProject([
                'colorMode' => $value,
                'colorModeAllowVisitor' => $value,
                'colorModeSwitchMenu' => $value,
                'colorModeSwitchMenuView' => $value,
                'colorModeSwitchFooter' => $value
            ]);

            self::assertSame([
                'defaultMode' => 'system',
                'allowVisitor' => false,
                'colorScheme' => 'light dark',
                'menuSwitch' => 'off',
                'menuView' => 'compact',
                'footerSwitch' => 'off',
                'storageKey' => 'quiqqer-template-presentation:colorMode:demo'
            ], ColorMode::getSettings($Project));
        }
    }

    public function testModesAndVisitorPermissionAreNormalizedIndependently(): void
    {
        foreach (['light', 'dark', 'system'] as $mode) {
            foreach ([true, 1, '1', false, 0, '0', 'false'] as $allowed) {
                $settings = ColorMode::getSettings($this->createProject([
                    'colorMode' => $mode,
                    'colorModeAllowVisitor' => $allowed
                ]));

                self::assertSame($mode, $settings['defaultMode']);
                self::assertSame(in_array($allowed, [true, 1, '1'], true), $settings['allowVisitor']);
                self::assertSame($mode === 'system' ? 'light dark' : $mode, $settings['colorScheme']);
            }
        }
    }

    public function testSwitchPlacementsRequireVisitorPermission(): void
    {
        foreach ([true, false] as $allowed) {
            foreach (['both', 'desktop', 'mobile', 'off', 'invalid', false] as $menu) {
                foreach (['icons', 'compact', 'dropdown', 'off', 'choice', false] as $footer) {
                    $settings = ColorMode::getSettings($this->createProject([
                        'colorModeAllowVisitor' => $allowed,
                        'colorModeSwitchMenu' => $menu,
                        'colorModeSwitchFooter' => $footer
                    ]));

                    $expectedMenu = 'off';
                    $expectedFooter = 'off';

                    if ($allowed) {
                        $expectedMenu = in_array($menu, ['both', 'desktop', 'mobile', 'off'], true) ? $menu : 'off';
                        $expectedFooter = in_array($footer, ['icons', 'compact', 'dropdown', 'off'], true) ? $footer : 'dropdown';
                    }

                    self::assertSame($expectedMenu, $settings['menuSwitch']);
                    self::assertSame($expectedFooter, $settings['footerSwitch']);
                }
            }
        }
    }

    public function testMenuViewFallsBackToCompact(): void
    {
        foreach (['compact' => 'compact', 'dropdown' => 'dropdown', 'icons' => 'compact', '' => 'compact'] as $view => $expected) {
            $settings = ColorMode::getSettings($this->createProject([
                'colorModeAllowVisitor' => true,
                'colorModeSwitchMenuView' => $view
            ]));

            self::assertSame($expected, $settings['menuView']);
        }
    }

    public function testStorageKeyIsPerProjectAndLanguageIndependent(): void
    {
        $first = ColorMode::getSettings($this->createProject([], 'first'))['storageKey'];
        $second = ColorMode::getSettings($this->createProject([], 'second'))['storageKey'];

        self::assertNotSame($first, $second);
        self::assertStringEndsWith(':first', $first);
    }

    public function testSettingsEditorDefaults(): void
    {
        $Dom = new \DOMDocument();
        $Dom->load(dirname(__DIR__, 3) . '/settings.xml');
        $Path = new \DOMXPath($Dom);
        self::assertSame('system', $Path->evaluate(
            'string(//select[@conf="templatePresentation.settings.colorMode"]/option[@selected="selected"]/@value)'
        ));
        self::assertSame('system', $Path->evaluate(
            'string(//select[@conf="templatePresentation.settings.colorMode"]/option[1]/@value)'
        ));
        self::assertSame('off', $Path->evaluate(
            'string(//select[@conf="templatePresentation.settings.colorModeSwitchMenu"]/option[@selected="selected"]/@value)'
        ));
        self::assertSame('off', $Path->evaluate('string(//conf[@name="colorModeSwitchMenu"]/defaultvalue)'));
        self::assertSame('dropdown', $Path->evaluate(
            'string(//select[@conf="templatePresentation.settings.colorModeSwitchFooter"]/option[@selected="selected"]/@value)'
        ));
        self::assertSame('dropdown', $Path->evaluate('string(//conf[@name="colorModeSwitchFooter"]/defaultvalue)'));
        self::assertSame('compact', $Path->evaluate('string(//conf[@name="colorModeSwitchMenuView"]/defaultvalue)'));
    }

    public function testDocumentDeclaresNativeColorSchemeBeforeStylesheets(): void
    {
        $html = file_get_contents(dirname(__DIR__, 3) . '/index.html');
        self::assertIsString($html);
        $style = '<style data-no-cache="1">:root { color-scheme: {$colorMode.colorScheme}; }</style>';
        self::assertStringContainsString($style, $html);
        self::assertStringContainsString('<meta name="color-scheme"', $html);
        self::assertLessThan(strpos($html, '{$Template->getHeader()}'), strpos($html, $style));
    }

    public function testStoredVisitorChoiceIsAppliedBeforeStylesheetsAndExcludedFromCacheBundling(): void
    {
        $html = file_get_contents(dirname(__DIR__, 3) . '/index.html');
        self::assertIsString($html);

        // quiqqer/cache moves inline scripts without data-no-cache into the bundle at the page end
        $script = strpos($html, '<script data-no-cache="1">');
        self::assertIsInt($script);
        self::assertLessThan(strpos($html, '{$Template->getHeader()}'), $script);
        self::assertStringContainsString('data-presentation-mode-storage', $html);
    }
}
