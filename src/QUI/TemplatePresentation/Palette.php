<?php

declare(strict_types=1);

namespace QUI\TemplatePresentation;

use QUI\Projects\Colors;
use QUI\Projects\Colors\PaletteProviderInterface;
use QUI\Projects\Project;
use QUI\Utils\Text\XML;

/**
 * Declarative standard only. Provider discovery and applying sets belong to ticket
 * https://dev.quiqqer.com/quiqqer/template-presentation/-/work_items/48.
 */
final class Palette implements PaletteProviderInterface
{
    /** Read the package default, independently of saved project settings. */
    public static function getDefaultPrimaryColor(): string
    {
        $settings = XML::getConfigParamsFromXml(dirname(__DIR__, 3) . '/settings.xml');
        $color = $settings['templatePresentation.settings']['colorMain']['default'] ?? null;

        if (!is_string($color) || !Colors::isColor($color)) {
            throw new \QUI\Exception('Missing or invalid Presentation primary color default in settings.xml.');
        }

        return $color;
    }

    public function getProviderId(): string
    {
        return 'quiqqer/template-presentation';
    }

    public function getDefaultPaletteId(Project $Project): string
    {
        return 'standard';
    }

    public function getPalettes(Project $Project): array
    {
        $colors = Colors::defaults();
        $primary = self::getDefaultPrimaryColor();
        $colors['primary']['value'] = $primary;
        $colors['link']['light'] = $primary;
        $colors['link-hover']['light'] = '#243e6d';

        return [[
            'id' => 'standard',
            'name' => ['group' => 'quiqqer/template-presentation', 'var' => 'package.title'],
            'roles' => [],
            'colors' => $colors
        ]];
    }
}
