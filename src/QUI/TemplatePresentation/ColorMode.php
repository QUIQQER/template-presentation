<?php

declare(strict_types=1);

namespace QUI\TemplatePresentation;

use QUI\Projects\Project;

/** Presentation owns the document mode; Core owns stored color conversion. */
final class ColorMode
{
    private const PREFIX = 'templatePresentation.settings.';

    /** @return array{defaultMode: string, allowVisitor: bool, colorScheme: string} */
    public static function getSettings(Project $Project): array
    {
        $mode = $Project->getConfig(self::PREFIX . 'colorMode');

        if (!in_array($mode, ['light', 'dark', 'system'], true)) {
            $mode = 'system';
        }

        return [
            'defaultMode' => $mode,
            'allowVisitor' => in_array(
                $Project->getConfig(self::PREFIX . 'colorModeAllowVisitor'),
                [true, 1, '1'],
                true
            ),
            'colorScheme' => $mode === 'system' ? 'light dark' : $mode
        ];
    }
}
