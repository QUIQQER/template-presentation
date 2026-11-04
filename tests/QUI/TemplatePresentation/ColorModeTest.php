<?php

declare(strict_types=1);

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI\Projects\Project;
use QUI\TemplatePresentation\ColorMode;

final class ColorModeTest extends TestCase
{
    public function testMissingAndInvalidSettingsUseSystem(): void
    {
        foreach ([false, '', 'invalid', ['dark']] as $mode) {
            $Project = $this->createMock(Project::class);
            $Project->method('getConfig')->willReturn($mode);
            self::assertSame([
                'defaultMode' => 'system',
                'allowVisitor' => false,
                'colorScheme' => 'light dark'
            ], ColorMode::getSettings($Project));
        }
    }

    public function testModesAndVisitorPermissionAreNormalizedIndependently(): void
    {
        foreach (['light', 'dark', 'system'] as $mode) {
            foreach ([true, 1, '1', false, 0, '0', 'false'] as $allowed) {
                $Project = $this->createMock(Project::class);
                $Project->method('getConfig')->willReturnCallback(
                    static fn (string $key): mixed => str_ends_with($key, 'colorMode') ? $mode : $allowed
                );
                self::assertSame([
                    'defaultMode' => $mode,
                    'allowVisitor' => in_array($allowed, [true, 1, '1'], true),
                    'colorScheme' => $mode === 'system' ? 'light dark' : $mode
                ], ColorMode::getSettings($Project));
            }
        }
    }

    public function testSettingsEditorDefaultsToSystemWhenProjectDataHasNoMode(): void
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
}
