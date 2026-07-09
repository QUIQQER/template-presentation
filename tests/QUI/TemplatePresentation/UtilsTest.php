<?php

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI\TemplatePresentation\Utils;

class UtilsTest extends TestCase
{
    public function testConvertBrickCssClassReturnsEmptyStringForEmptyInput(): void
    {
        $this->assertSame('', Utils::convertBrickCSSClass([]));
    }

    public function testConvertBrickCssClassPrefixesAllClasses(): void
    {
        $this->assertSame(
            ' brick-container__hero brick-container__highlight',
            Utils::convertBrickCSSClass(['hero', 'highlight'])
        );
    }

    public function testGetSpacingVariableReturnsExpectedCssDeclaration(): void
    {
        $this->assertSame(
            '--qui-tpl-spacing--top: var(--qui-row-spacing--large);',
            Utils::getSpacingVariable('large', 'top')
        );
    }

    public function testGetSpacingVariableFallsBackToBaseForEmptyValue(): void
    {
        $this->assertSame(
            '--qui-tpl-spacing--bottom: var(--qui-row-spacing--base);',
            Utils::getSpacingVariable('', 'bottom')
        );
    }

    public function testGetSpacingVariableReturnsEmptyStringForInvalidPosition(): void
    {
        $this->assertSame('', Utils::getSpacingVariable('large', 'left'));
    }

    public function testGetCustomVariableBuildsThemeAwareCssDeclaration(): void
    {
        $this->assertSame(
            '--qui-tpl-nav-gap: var(--theme--qui-tpl-nav-gap,12px);',
            Utils::getCustomVariable('nav-gap', '12px')
        );
    }

    public function testGetCustomVariableReturnsEmptyStringForMissingParts(): void
    {
        $this->assertSame('', Utils::getCustomVariable('', '12px'));
        $this->assertSame('', Utils::getCustomVariable('nav-gap', ''));
    }

    public function testResolveLocalizedConfigTextReturnsPlainStringWhenNotJson(): void
    {
        $this->assertSame(
            'Logo title',
            $this->invokeResolveLocalizedConfigText('Logo title', 'de')
        );
    }

    public function testResolveLocalizedConfigTextReturnsLocalizedJsonValue(): void
    {
        $json = '{"de":" Startseite ","en":"Home"}';

        $this->assertSame('Startseite', $this->invokeResolveLocalizedConfigText($json, 'de'));
    }

    public function testResolveLocalizedConfigTextReturnsEmptyStringForUnknownLanguage(): void
    {
        $json = '{"en":"Home"}';

        $this->assertSame('', $this->invokeResolveLocalizedConfigText($json, 'de'));
    }

    public function testResolveLocalizedConfigTextReturnsEmptyStringForNonStringValue(): void
    {
        $this->assertSame('', $this->invokeResolveLocalizedConfigText(['de' => 'Home'], 'de'));
    }

    private function invokeResolveLocalizedConfigText(mixed $value, string $language): string
    {
        $Reflection = new \ReflectionClass(Utils::class);
        $method = $Reflection->getMethod('resolveLocalizedConfigText');
        $method->setAccessible(true);

        return $method->invoke(null, $value, $language);
    }
}
