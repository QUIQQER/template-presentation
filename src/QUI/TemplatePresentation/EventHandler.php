<?php

/**
 * This file contains \QUI\TemplatePresentation\EventHandler
 */

namespace QUI\TemplatePresentation;

use QUI;
use QUI\Projects\Colors;
use Smarty;

/**
 * Event Class
 */
class EventHandler
{
    /**
     * Remove obsolete generated assets from the package directory.
     */
    public static function onPackageSetup(QUI\Package\Package $Package): void
    {
        if ($Package->getName() !== 'quiqqer/template-presentation') {
            return;
        }

        $legacyCssFile = $Package->getDir() . 'bin/css/project-settings.css';

        if (is_file($legacyCssFile)) {
            @unlink($legacyCssFile);
        }
    }

    /**
     * Clear system cache on project save.
     *
     * @param string $projectName
     * @param array<string, mixed> $config
     * @param array<string, mixed> $params
     * @return void
     */
    public static function onProjectConfigSave(string $projectName, array $config, array $params): void
    {
        QUI\Cache\Manager::clear('quiqqer/templatePresentation');
    }

    /** @param array<string, mixed> $settings */
    public static function onEditorGetSettings(QUI\Projects\Project $Project, array &$settings): void
    {
        $template = $Project->getTemplate();

        if (
            !$template || ($template !== 'quiqqer/template-presentation'
                && QUI::getPackage($template)->getTemplateParent()?->getName() !== 'quiqqer/template-presentation')
        ) {
            return;
        }

        $css = ':root {';

        foreach (
            [
            'colorMain' => '--template-settings__color-primary',
            'colorMainContrast' => '--template-settings__color-primary-contrast',
            'buttonFontColor' => '--template-settings__btn-text-color'
            ] as $key => $variable
        ) {
            $value = Colors::toCss($Project->getConfig('templatePresentation.settings.' . $key));

            if ($key === 'colorMain' && $value === '') {
                $value = Palette::getDefaultPrimaryColor();
            }

            if ($value !== '') {
                $css .= $variable . ':' . $value . ';';
            }
        }

        $settings['cssFiles'][] = 'data:text/css;base64,' . base64_encode($css . '}');
    }

    /**
     * Clear system cache on site save
     */
    public static function onSiteSave(QUI\Interfaces\Projects\Site $Site): void
    {
        $Project = $Site->getProject();
        $cacheName = md5($Project->getName() . $Project->getLang() . $Site->getId());

        QUI\Cache\Manager::clear('quiqqer/templatePresentation/' . $cacheName);
    }

    /**
     * Event: on smarty init
     *
     * @param Smarty $Smarty
     * @return void
     */
    public static function onSmartyInit(Smarty $Smarty): void
    {
        if (!isset($Smarty->registered_classes['QUI\TemplatePresentation\Utils'])) {
            $Smarty->registerClass('QUI\TemplatePresentation\Utils', '\QUI\TemplatePresentation\Utils');
        };
    }

    /**
     * Event: quiqqer/menu is building the mobile slide-out menu.
     *
     * Inject the bricks of the "navAction" area into the mobile menu, since
     * the desktop header suffix (which normally renders them) is hidden on
     * mobile. quiqqer/menu itself has no knowledge of this template area.
     *
     * @param QUI\Control $Menu - the mobile menu control (SlideOut/SlideOutAdvanced)
     * @param QUI\Interfaces\Projects\Site $Site
     * @return void
     */
    public static function onQuiqqerMenuMobileMenuCreate(
        QUI\Control $Menu,
        QUI\Interfaces\Projects\Site $Site
    ): void {
        if (
            !$Menu instanceof QUI\Menu\SlideOut
            && !$Menu instanceof QUI\Menu\SlideOutAdvanced
        ) {
            return;
        }

        $BricksManager = QUI\Bricks\Manager::init();

        if (!$BricksManager) {
            return;
        }

        try {
            $bricks = $BricksManager->getBricksByArea('navAction', $Site);
        } catch (QUI\Exception) {
            return;
        }

        if (empty($bricks)) {
            return;
        }

        $html = '<div class="tpl-brickArea-navAction tpl-brickArea-navAction--mobile">';

        foreach ($bricks as $Brick) {
            $html .= $Brick->create();
        }

        $html .= '</div>';

        $Menu->appendHTML($html);
    }
}
