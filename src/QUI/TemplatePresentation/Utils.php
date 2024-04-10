<?php

/**
 * This file contains QUI\TemplatePresentation\TemplateLoader
 */

namespace QUI\TemplatePresentation;

use QUI;

/**
 * Help Class for Template Presentation
 *
 * @return array
 * @author  www.pcsg.de (Michael Danielczok)
 *
 * @package QUI\TemplatePresentation
 */
class Utils
{
    /**
     * @param array $params
     *
     * @return array
     */
    public static function getConfig($params)
    {
        $cacheName = md5($params['Project']->getName() . $params['Project']->getLang() . $params['Site']->getId());

        try {
            return QUI\Cache\Manager::get(
                'quiqqer/templatePresentation/' . $cacheName
            );
        } catch (QUI\Exception $Exception) {
        }

        $config = [];

        /* @var $Project QUI\Projects\Project */
        $Project = $params['Project'];
        $Template = $params['Template'];

        $Site = $params['Site'];

        /**
         * no header?
         * no breadcrumb?
         *
         * own site type
         */

        $showHeader = false;
        $showBreadcrumb = false;

        switch ($Template->getLayoutType()) {
            case 'layout/startPage':
                $showHeader = $Project->getConfig('templatePresentation.settings.showHeaderStartPage');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbStartPage');
                break;

            case 'layout/noSidebar':
                $showHeader = $Project->getConfig('templatePresentation.settings.showHeaderNoSidebar');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbNoSidebar');
                break;

            case 'layout/noSidebarSmall':
                $showHeader = $Project->getConfig('templatePresentation.settings.showHeaderNoSidebarSmall');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbNoSidebarSmall');
                break;

            case 'layout/rightSidebar':
                $showHeader = $Project->getConfig('templatePresentation.settings.showHeaderRightSidebar');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbRightSidebar');
                break;

            case 'layout/leftSidebar':
                $showHeader = $Project->getConfig('templatePresentation.settings.showHeaderLeftSidebar');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbLeftSidebar');
                break;
        }

        $showPageTitle = false;
        $showPageShort = false;

        if ($Project->getConfig('templatePresentation.settings.showTitle')) {
            $showPageTitle = $Project->getConfig('templatePresentation.settings.showTitle');
        };

        if ($Project->getConfig('templatePresentation.settings.showShort')) {
            $showPageShort = $Project->getConfig('templatePresentation.settings.showShort');
        };

        /* site own show title */
        switch ($params['Site']->getAttribute('templatePresentation.showTitle')) {
            case 'show':
                $showPageTitle = true;
                break;
            case 'hide':
                $showPageTitle = false;
                break;
        }

        /* site own show short description */
        switch ($params['Site']->getAttribute('templatePresentation.showShort')) {
            case 'show':
                $showPageShort = true;
                break;
            case 'hide':
                $showPageShort = false;
                break;
        }

        /* site own show header */
        switch ($params['Site']->getAttribute('templatePresentation.showHeader')) {
            case 'show':
                $showHeader = true;
                break;
            case 'hide':
                $showHeader = false;
        }

        /* page custom class */
        $customClass = $Site->getAttribute('templatePresentation.pageCustomClass');
        $pageCustomClass = false;

        if ($customClass && $customClass !== '') {
            $pageCustomClass = 'templatePresentation__' . $customClass;
            $pageCustomClass .= ' ' . $customClass;
        }

        $headerArea = $params['headerArea'];
        $settingsCSS = include 'settings.css.php';

        $logoData = self::getLogoData($Project);

        /**
         * Include demo css
         */
        $includeDemoCss = $Project->getConfig('templatePresentation.settings.includeDemoStyling');

        $config += [
            'showHeader' => $showHeader,
            'showBreadcrumb' => $showBreadcrumb,
            'settingsCSS' => '<style data-no-cache="1">' . $settingsCSS . '</style>',
            'typeClass' => 'type-' . str_replace(['/', ':'], '-', $params['Site']->getAttribute('type')),
            'navPos' => $Project->getConfig('templatePresentation.settings.navPos'),
            'navAlignment' => $Project->getConfig('templatePresentation.settings.navAlignment'),
            'headerArea' => $headerArea,
            'showPageTitle' => $showPageTitle,
            'showPageShort' => $showPageShort,
            'pageCustomClass' => $pageCustomClass,
            'logoData' => $logoData,
            'useSlideOutMenu' => true, // for now is always true because quiqqer use currently only SlideOut nav
            'includeDemoCss' => $includeDemoCss
        ];

        // set cache
        QUI\Cache\Manager::set(
            'quiqqer/templatePresentation/' . $cacheName,
            $config
        );

        return $config;
    }

    /**
     * Add a suffix to brick css class(es)
     *
     * @param array $classes
     *
     * @return string
     */
    public static function convertBrickCSSClass(array $classes)
    {
        if (\count($classes) < 1) {
            return '';
        }

        $text = '';

        foreach ($classes as $classString) {
            $text .= ' brick-container__' . $classString;
        }

        return $text;
    }

    /**
     * Get logo data (url, alt, width, height) as an array
     *
     * @param $Project QUI\Projects\Project
     *
     * @return array
     */
    public static function getLogoData(QUI\Projects\Project $Project): array
    {
        $alt = "QUIQQER";
        $Logo = $Project->getMedia()->getLogoImage();
        $url = $Project->getMedia()->getPlaceholder();

        $navbarHeight = (int)$Project->getConfig('templatePresentation.settings.navBarHeight');
        $height = (int)$Project->getConfig('templatePresentation.settings.logoHeight');
        $width = (int)$Project->getConfig('templatePresentation.settings.logoWidth');

        if (!$height || $height < 0) {
            $height = $navbarHeight;
        }

        if ($Logo) {
            try {
                $alt = $Logo->getAttribute('title');
                $url = $Logo->getSizeCacheUrl(500, 200);

                if (!$width) {
                    $width = $Logo->getResizeSize(false, $height)['width'];
                }
            } catch (QUI\Exception $Exception) {
                QUI\System\Log::addNotice($Exception->getMessage());
            }
        }

        return [
            'url' => $url,
            'alt' => $alt,
            'width' => $width,
            'height' => $height
        ];
    }
}
