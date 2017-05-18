<?php

/**
 * This file contains QUI\TemplatePresentation\TemplateLoader
 */

namespace QUI\TemplatePresentation;

use QUI;

/**
 * Help Class for Template Presentation
 *
 * @package QUI\TemplatePresentation
 * @author www.pcsg.de (Michael Danielczok)
 *
 * @return array
 */
class Utils
{
    /**
     * @param array $params
     * @return array
     */
    public static function getConfig($params)
    {
        try {
            return QUI\Cache\Manager::get(
                'quiqqer/templatePresentation/' . $params['Site']->getId()
            );
        } catch (QUI\Exception $Exception) {
        }

        $config = array();

        /* @var $Project QUI\Projects\Project */
        $Project  = $params['Project'];
        $Template = $params['Template'];

        /**
         * no header?
         * no breadcrumb?
         * Body Class
         *
         * own site type
         */

        $showHeader     = false;
        $showBreadcrumb = false;
        $bodyClass      = '';

        switch ($Template->getLayoutType()) {
            case 'layout/startPage':
                $showHeader     = $Project->getConfig('templatePresentation.settings.showHeaderStartPage');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbStartPage');
                $bodyClass      = 'startpage';
                break;

            case 'layout/noSidebar':
                $showHeader     = $Project->getConfig('templatePresentation.settings.showHeaderNoSidebar');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbNoSidebar');
                $bodyClass      = 'left-sidebar';
                break;

            case 'layout/rightSidebar':
                $showHeader     = $Project->getConfig('templatePresentation.settings.showHeaderRightSidebar');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbRightSidebar');
                $bodyClass      = 'right-sidebar';
                break;

            case 'layout/leftSidebar':
                $showHeader     = $Project->getConfig('templatePresentation.settings.showHeaderLeftSidebar');
                $showBreadcrumb = $Project->getConfig('templatePresentation.settings.showBreadcrumbLeftSidebar');
                $bodyClass      = 'no-sidebar';
                break;
        }

        $startpageFirstHeaderBrick = false;

        if ($Template->getLayoutType() == 'layout/startPage' &&
            $Project->getConfig('templatePresentation.settings.startpageFirstHeaderBrick')
        ) {
            $startpageFirstHeaderBrick = true;
        }

        $settingsCSS = include 'settings.css.php';


        $config += array(
            'quiTplType'                => $Project->getConfig('templatePresentation.settings.standardType'),
            'showHeader'                => $showHeader,
            'showBreadcrumb'            => $showBreadcrumb,
            'settingsCSS'               => '<style>' . $settingsCSS . '</style>',
            'typeClass'                 => 'type-' . str_replace(array('/', ':'), '-',
                    $params['Site']->getAttribute('type')),
            'bodyClass'                 => $bodyClass,
            'navPos'                    => $Project->getConfig('templatePresentation.settings.navPos'),
            'startpageFirstHeaderBrick' => $startpageFirstHeaderBrick
        );


        // set cache
        QUI\Cache\Manager::set(
            'quiqqer/templatePresentation/' . $params['Site']->getId(),
            $config
        );

        return $config;
    }
}
