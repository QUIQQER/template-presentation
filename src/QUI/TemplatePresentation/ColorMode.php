<?php

declare(strict_types=1);

namespace QUI\TemplatePresentation;

use QUI\Projects\Project;

/** Presentation owns the document mode; Core owns stored color conversion. */
final class ColorMode
{
    private const PREFIX = 'templatePresentation.settings.';

    private const MENU_SWITCH_PLACEMENTS = ['both', 'desktop', 'mobile', 'off'];

    private const MENU_SWITCH_VIEWS = ['compact', 'dropdown'];

    private const FOOTER_SWITCH_VIEWS = ['icons', 'compact', 'dropdown', 'off'];

    /**
     * The visitor switch settings are only effective when visitors may change the mode.
     * The storage key is per project and language independent.
     *
     * @return array{
     *     defaultMode: string,
     *     allowVisitor: bool,
     *     colorScheme: string,
     *     menuSwitch: string,
     *     menuView: string,
     *     footerSwitch: string,
     *     storageKey: string
     * }
     */
    public static function getSettings(Project $Project): array
    {
        $mode = $Project->getConfig(self::PREFIX . 'colorMode');

        if (!in_array($mode, ['light', 'dark', 'system'], true)) {
            $mode = 'system';
        }

        $allowVisitor = self::isEnabled($Project->getConfig(self::PREFIX . 'colorModeAllowVisitor'));
        $menuSwitch = $Project->getConfig(self::PREFIX . 'colorModeSwitchMenu');
        $footerSwitch = $Project->getConfig(self::PREFIX . 'colorModeSwitchFooter');

        if (!$allowVisitor) {
            $menuSwitch = 'off';
            $footerSwitch = 'off';
        }

        if (!in_array($menuSwitch, self::MENU_SWITCH_PLACEMENTS, true)) {
            $menuSwitch = 'off';
        }

        if (!in_array($footerSwitch, self::FOOTER_SWITCH_VIEWS, true)) {
            $footerSwitch = 'dropdown';
        }

        $menuView = $Project->getConfig(self::PREFIX . 'colorModeSwitchMenuView');

        if (!in_array($menuView, self::MENU_SWITCH_VIEWS, true)) {
            $menuView = 'compact';
        }

        return [
            'defaultMode' => $mode,
            'allowVisitor' => $allowVisitor,
            'colorScheme' => $mode === 'system' ? 'light dark' : $mode,
            'menuSwitch' => $menuSwitch,
            'menuView' => $menuView,
            'footerSwitch' => $footerSwitch,
            'storageKey' => 'quiqqer-template-presentation:colorMode:' . $Project->getName()
        ];
    }

    private static function isEnabled(mixed $value): bool
    {
        return in_array($value, [true, 1, '1'], true);
    }
}
