<?php

/**
 * This file contains QUI\TemplatePresentation\Utils
 */

namespace QUI\TemplatePresentation;

use QUI;
use QUI\Exception;

use function count;

/**
 * Help Class for Template Presentation
 *
 * @return array
 * @author  www.pcsg.de (Michael Danielczok)
 * @package QUI\TemplatePresentation
 */
class Utils
{
    private static QUI\Projects\Project $Project;

    /**
     * @param Array<string, mixed> $params
     *
     * @return Array<string, mixed>
     * @throws Exception
     */
    public static function getConfig(array $params): array
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

        self::$Project = $Project;

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
        $headerTextColor = 'inherit';
        $headerTextPos = 'center';
        $mainContentSpacingTop = 'base';
        $mainContentSpacingBottom = 'base';

        if ($Project->getConfig('templatePresentation.settings.showTitle')) {
            $showPageTitle = $Project->getConfig('templatePresentation.settings.showTitle');
        }

        if ($Project->getConfig('templatePresentation.settings.showShort')) {
            $showPageShort = $Project->getConfig('templatePresentation.settings.showShort');
        }

        if ($Project->getConfig('templatePresentation.settings.header.textColor')) {
            $headerTextColor = $Project->getConfig('templatePresentation.settings.header.textColor');
        }

        if ($Project->getConfig('templatePresentation.settings.header.textPos')) {
            $headerTextPos = $Project->getConfig('templatePresentation.settings.header.textPos');
        }

        if ($Project->getConfig('templatePresentation.settings.mainContentSpacingTop')) {
            $mainContentSpacingTop = $Project->getConfig('templatePresentation.settings.mainContentSpacingTop');
        }

        if ($Project->getConfig('templatePresentation.settings.mainContentSpacingBottom')) {
            $mainContentSpacingBottom = $Project->getConfig('templatePresentation.settings.mainContentSpacingBottom');
        }

        /* site own show title */
        switch ($Site->getAttribute('templatePresentation.showTitle')) {
            case 'show':
                $showPageTitle = true;
                break;
            case 'hide':
                $showPageTitle = false;
                break;
        }

        /* site own show short description */
        switch ($Site->getAttribute('templatePresentation.showShort')) {
            case 'show':
                $showPageShort = true;
                break;
            case 'hide':
                $showPageShort = false;
                break;
        }

        /* site own show header */
        switch ($Site->getAttribute('templatePresentation.showHeader')) {
            case 'show':
                $showHeader = true;
                break;
            case 'hide':
                $showHeader = false;
                break;
        }

        /* site own header text color */
        switch ($Site->getAttribute('templatePresentation.header.textColor.enable')) {
            case 'useSiteSetting':
                $headerTextColor = $Site->getAttribute('templatePresentation.header.textColor.color');
                break;
            case 'useDefaultColor':
                $headerTextColor = 'inherit';
                break;
        }

        /* site own header text position */
        switch ($Site->getAttribute('templatePresentation.header.textPos')) {
            case 'flex-start':
            case 'center':
            case 'flex-right':
                $headerTextPos = $Site->getAttribute('templatePresentation.header.textPos');
        }

        // make text alignment depend on content (flexbox) alignment
        $headerTextAlignment = match ($headerTextPos) {
            'flex-start' => 'left',
            'flex-end' => 'right',
            default => 'center',
        };

        /* site own main content spacing top */
        switch ($Site->getAttribute('templatePresentation.mainContent.spacingTop')) {
            case 'disabled':
            case 'small':
            case 'base':
            case 'medium':
            case 'large':
            case 'extraLarge':
                $mainContentSpacingTop = $Site->getAttribute('templatePresentation.mainContent.spacingTop');
        }

        /* site own main content spacing bottom */
        switch ($Site->getAttribute('templatePresentation.mainContent.spacingBottom')) {
            case 'disabled':
            case 'small':
            case 'base':
            case 'medium':
            case 'large':
            case 'extraLarge':
                $mainContentSpacingBottom = $Site->getAttribute('templatePresentation.mainContent.spacingBottom');
        }

        /* page custom class */
        $customClass = $Site->getAttribute('templatePresentation.pageCustomClass');
        $pageCustomClass = false;

        if ($customClass && $customClass !== '') {
            $pageCustomClass = 'templatePresentation__' . $customClass;
            $pageCustomClass .= ' ' . $customClass;
        }

        /**
         * Dropdown Language switch
         */
        $showLangSelect = false;
        $showFlag = false;
        $showText = false;

        switch ($Project->getConfig('templatePresentation.settings.dropdownLangNav')) {
            case 'flag':
                $showFlag = true;
                $showLangSelect = true;
                break;

            case 'text':
                $showText = 'abbreviation';
                $showLangSelect = true;
                break;

            case 'flagAndText':
                $showFlag = true;
                $showText = 'abbreviation';
                $showLangSelect = true;
                break;
        }

        /**
         * css settings
         */
        $headerArea = $params['headerArea'];
        $cssVariables = self::getCssVariables($headerArea, $showHeader);

        /**
         * Include demo css
         */
        $includeDemoCss = $Project->getConfig('templatePresentation.settings.includeDemoStyling');

        $searchData = self::getSearchData();

        $config += [
            'showHeader' => $showHeader,
            'showBreadcrumb' => $showBreadcrumb,
            'cssVariables' => $cssVariables,
            'typeClass' => 'type-' . str_replace(['/', ':'], '-', $Site->getAttribute('type')),
            'navPos' => $Project->getConfig('templatePresentation.settings.navPos'),
            'navAlignment' => $Project->getConfig('templatePresentation.settings.navAlignment'),
            'headerArea' => $headerArea,
            'showPageTitle' => $showPageTitle,
            'showPageShort' => $showPageShort,
            'pageCustomClass' => $pageCustomClass,
            'logoSize' => self::getLogoSize(),
            'useSlideOutMenu' => true, // for now is always true because quiqqer use currently only SlideOut nav
            'includeDemoCss' => $includeDemoCss,
            'headerTextColor' => $headerTextColor,
            'headerTextPos' => $headerTextPos,
            'headerTextAlignment' => $headerTextAlignment,
            'mainContentSpacingTopCSSVar' => self::getSpacingVariable($mainContentSpacingTop, 'top'),
            'mainContentSpacingBottomCSSVar' => self::getSpacingVariable($mainContentSpacingBottom, 'bottom'),
            'socialData' => self::getSocialLinks(),

            'showLangSelect' => $showLangSelect,
            'showFlag' => $showFlag,
            'showText' => $showText,

            'searchType' => $searchData['searchType'],
            'noSearch' => $searchData['noSearch'],
            'searchUrl' => $searchData['searchUrl'],
            'searchDataQui' => $searchData['searchDataQui'],
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
     * @param string[] $classes
     *
     * @return string
     */
    public static function convertBrickCSSClass(array $classes): string
    {
        if (count($classes) < 1) {
            return '';
        }

        $text = '';

        foreach ($classes as $classString) {
            $text .= ' brick-container__' . $classString;
        }

        return $text;
    }

    // region brick settings

    /**
     * Get logo size as an array
     *
     * @return array {width: int, height: int}
     */
    private static function getLogoSize(): array
    {
        $Logo = self::$Project->getMedia()->getLogoImage();
        $navbarHeight = (int)self::$Project->getConfig('templatePresentation.settings.navBarHeight');
        $height = (int)self::$Project->getConfig('templatePresentation.settings.logoHeight');
        $width = (int)self::$Project->getConfig('templatePresentation.settings.logoWidth');

        if (!$height || $height < 0) {
            $height = $navbarHeight;
        }

        if ($Logo) {
            try {
                if (!$width) {
                    $width = $Logo->getResizeSize(false, $height)['width'];
                }
            } catch (QUI\Exception $Exception) {
                QUI\System\Log::addNotice($Exception->getMessage());
            }
        }

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * Get css variable declaration for background color depend on given brick setting
     *
     * @param QUI\Bricks\Brick $Brick
     * @return string
     * @throws QUI\Exception
     */
    public static function getBrickBgColorCssVar(QUI\Bricks\Brick $Brick): string
    {
        $setting = $Brick->getSetting('enableBrickBgColor');

        if ($setting === 'disable') {
            return '';
        }

        $bgColor = $Brick->getSetting('brickBgColor');

        if ($setting === 'enable.useTemplateSetting') {
            $Project = QUI::getRewrite()->getProject();
            $bgColor = $Project->getConfig('templatePresentation.settings.bricks.bgColor');
        }

        if (!$bgColor) {
            return '';
        }

        return '--qui-tpl-brick-backgroundColor: ' . $bgColor . ';';
    }

    /**
     * Get css variable declaration for text color depend on given brick setting
     *
     * @param QUI\Bricks\Brick $Brick
     * @return string
     * @throws QUI\Exception
     */
    public static function getBrickTextColorCssVar(QUI\Bricks\Brick $Brick): string
    {
        $setting = $Brick->getSetting('enableBrickTextColor');

        if ($setting === 'disable') {
            return '';
        }

        $color = $Brick->getSetting('brickTextColor');

        if ($setting === 'enable.useTemplateSetting') {
            $Project = QUI::getRewrite()->getProject();
            $color = $Project->getConfig('templatePresentation.settings.bricks.textColor');
        }

        if (!$color) {
            return '';
        }

        return '--qui-tpl-brick-textColor: ' . $color . ';';
    }

    // endregion

    /**
     * Return spacing css variable declaration (top or bottom).
     * This row spacing variable is defined in template variable.css file.
     *
     * Examples:
     *    --qui-tpl-spacing--top:  var(--qui-row-spacing--base);
     *    --qui-tpl-spacing--top:  var(--qui-row-spacing--small);
     *    --qui-tpl-spacing--bottom:  var(--qui-row-spacing--extraLarge);
     *
     * @param string $value // allowed values: 'disabled', 'extraSmall', 'small', 'base', 'medium', 'large', 'extraLarge'
     * @param string $pos // only 'top' and 'bottom' are allowed
     * @return string
     */
    public static function getSpacingVariable(string $value, string $pos): string
    {
        if (!$pos) {
            return '';
        }

        if (!$value) {
            $value = 'base';
        }

        if ($pos !== 'top' && $pos !== 'bottom') {
            return '';
        }

        return '--qui-tpl-spacing--' . $pos . ': var(--qui-row-spacing--' . $value . ');';
    }

    /**
     * Get custom css variable declaration by given name and value.
     *
     * Examples:
     *    --qui-tpl-VAR_NAME: var(--theme--qui-tpl-VAR_NAME, VALUE);
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    public static function getCustomVariable(string $name, string $value): string
    {
        if (!$name || !$value) {
            return '';
        }

        $variable = '--qui-tpl-' . $name . ': ';
        $value = 'var(--theme--qui-tpl-' . $name . ',' . $value . ');';

        return $variable . $value;
    }

    /**
     * Returns social links as an array with URL and icon
     *
     * @return array<int, array{url: string, icon: string}>
     */
    public static function getSocialLinks(): array
    {
        $socials = [
            'facebook' => 'fa-facebook',
            'twitter' => 'fa-twitter',
            'google' => 'fa-google-plus',
            'youtube' => 'fa-youtube-play',
            'github' => 'fa-github',
            'gitlab' => 'fa-gitlab',
        ];
        $result = [];
        foreach ($socials as $key => $icon) {
            $url = self::$Project->getConfig('templatePresentation.settings.social.' . $key);
            if (!empty($url)) {
                $result[] = [
                    'url' => $url,
                    'icon' => $icon
                ];
            }
        }

        return $result;
    }

    /**
     * Returns search data and settings for the template.
     *
     * @return array{
     *     searchType: string,
     *     searchUrl: string,
     *     searchDataQui: string,
     *     noSearch: string,
     *     inputSearch: string
     * }
     * @throws QUI\Exception If an error occurs while fetching the search sites.
     */
    public static function getSearchData(): array
    {
        $searchType = '';
        $searchUrl = '';
        $searchDataQui = '';
        $noSearch = 'no-search';
        $inputSearch = '';

        if (self::$Project->getConfig('templatePresentation.settings.search') != 'hide') {
            $types = [
                'quiqqer/sitetypes:types/search'
            ];

            /* check if quiqqer search packet is installed */
            if (QUI::getPackageManager()->isInstalled('quiqqer/search')) {
                $types = [
                    'quiqqer/sitetypes:types/search',
                    'quiqqer/search:types/search'
                ];

                // Suggest Search integrate
                $searchDataQui = 'data-qui="package/quiqqer/search/bin/controls/Suggest"';
            }

            $searchSites = self::$Project->getSites([
                'where' => [
                    'type' => [
                        'type' => 'IN',
                        'value' => $types
                    ]
                ],
                'limit' => 1
            ]);

            if (count($searchSites)) {
                try {
                    $noSearch = '';
                    $searchUrl = $searchSites[0]->getUrlRewritten();

                    switch (self::$Project->getConfig('templatePresentation.settings.search')) {
                        case 'input':
                            $inputSearch = 'input-search';
                            $searchType = 'input';
                            break;

                        case 'inputAndIcon':
                            $searchType = 'inputAndIcon';
                            break;
                    }
                } catch (QUI\Exception $Exception) {
                    QUI\System\Log::addNotice($Exception->getMessage());
                }
            }
        }

        return [
            'searchType' => $searchType,
            'searchUrl' => $searchUrl,
            'searchDataQui' => $searchDataQui,
            'noSearch' => $noSearch,
            'inputSearch' => $inputSearch
        ];
    }

    /**
     * Returns an array of CSS variables for the template presentation.
     * These variables include general layout, color, typography, navigation,
     * page header, and footer settings, which are used to dynamically generate
     * CSS custom properties for the frontend.
     *
     * @param bool $headerArea Whether the header area is enabled (affects certain CSS variables).
     * @param bool $showHeader Whether the page header (hero) is enabled (affects certain CSS variables).
     * @return array Associative array of CSS variable names and their values.
     */
    protected static function getCssVariables(bool $headerArea = false, bool $showHeader = false): array
    {
        /**
         * colors & typography
         */
        $bodyColor = '#3b3b3a';
        $bodyFontSize = '16px';
        $bodyFontWeight = '400'; /* normal */
        $headingColor = '';
        $headingFontWeight = '700'; /* bold */

        $colorMain = '#2e4d87';
        $btnTextColor = '#ffffff';

        // footer
        $footerBgColor = '#414141';
        $footerTextColor = '#d1d1d1';
        $footerLinkColor = '#aaaaaa';

        if (self::$Project->getConfig('templatePresentation.settings.colorFooterBackground')) {
            $footerBgColor = self::$Project->getConfig('templatePresentation.settings.colorFooterBackground');
        }

        if (self::$Project->getConfig('templatePresentation.settings.colorFooterFont')) {
            $footerTextColor = self::$Project->getConfig('templatePresentation.settings.colorFooterFont');
        }

        if (self::$Project->getConfig('templatePresentation.settings.colorMain')) {
            $colorMain = self::$Project->getConfig('templatePresentation.settings.colorMain');
        }

        if (self::$Project->getConfig('templatePresentation.settings.buttonFontColor')) {
            $btnTextColor = self::$Project->getConfig('templatePresentation.settings.buttonFontColor');
        }

        if (self::$Project->getConfig('templatePresentation.settings.colorMainContentFont')) {
            $bodyColor = self::$Project->getConfig('templatePresentation.settings.colorMainContentFont');
        }

        if (self::$Project->getConfig('templatePresentation.settings.typography.text.fontWeight')) {
            $bodyFontWeight = self::$Project->getConfig('templatePresentation.settings.typography.text.fontWeight');
        }

        if (self::$Project->getConfig('templatePresentation.settings.typography.text.fontSize')) {
            $bodyFontSize = self::$Project->getConfig('templatePresentation.settings.typography.text.fontSize') . 'px';
        }

        if (self::$Project->getConfig('templatePresentation.settings.headingColor')) {
            $headingColor = self::$Project->getConfig('templatePresentation.settings.headingColor');
        }

        if (self::$Project->getConfig('templatePresentation.settings.typography.heading.fontWeight')) {
            $headingFontWeight = self::$Project->getConfig('templatePresentation.settings.typography.heading.fontWeight');
        }

        /**
         * Nav
         */
        $navBgColor = '#2d4d88';
        $navBarFontColor = '#ffffff';
        $navMobileTextColor = '#ffffff';
        $navMobileBgColor = '#252122';
        $navHeight = (int)self::$Project->getConfig('templatePresentation.settings.navBarHeight');
        $navPos = self::$Project->getConfig('templatePresentation.settings.navPos');

        if (self::$Project->getConfig('templatePresentation.settings.navBarMainColor')) {
            $navBgColor = self::$Project->getConfig('templatePresentation.settings.navBarMainColor');
        }

        $navBgColorScrolled = $navBgColor;

        if (self::$Project->getConfig('templatePresentation.settings.navBarFontColor')) {
            $navBarFontColor = self::$Project->getConfig('templatePresentation.settings.navBarFontColor');
        }

        if (self::$Project->getConfig('templatePresentation.settings.mobileFontColor')) {
            $navMobileTextColor = self::$Project->getConfig('templatePresentation.settings.mobileFontColor');
        }

        if (self::$Project->getConfig('templatePresentation.settings.mobileMenuBackground')) {
            $navMobileBgColor = self::$Project->getConfig('templatePresentation.settings.mobileMenuBackground');
        }

        $navPositionCSS = 'absolute';
        $bodyContainerTop = $navHeight;

        if ($navPos == 'fix') {
            $navPositionCSS = 'fixed';
        }

        /**
         * Page header
         */
        $pageHeaderMinHeightDesktop = (int)self::$Project->getConfig('templatePresentation.settings.headerHeightValue');
        $pageHeaderMinHeightMobile = (int)self::$Project->getConfig(
            'templatePresentation.settings.headerHeightValueMobile'
        );
        $pageHeaderTextAlignment = self::$Project->getConfig('templatePresentation.settings.pageHeaderTextAlignment');
        $pageHeaderHeadingColor = self::$Project->getConfig('templatePresentation.settings.pageHeaderHeadingColor');
        $pageHeaderTextColor = self::$Project->getConfig('templatePresentation.settings.pageHeaderTextColor');

        if (!$pageHeaderMinHeightMobile) {
            $pageHeaderMinHeightMobile = $pageHeaderMinHeightDesktop;
        }

        if (!$pageHeaderHeadingColor) {
            $pageHeaderHeadingColor = 'inherit';
        }

        if (!$pageHeaderTextColor) {
            $pageHeaderTextColor = 'inherit';
        }

        $pageHeaderImgPosition = self::$Project->getConfig('templatePresentation.settings.headerImagePosition');

        /**
         * Others
         */
        $scrollOffset = 0;
        if ($navPos == 'fix' && $navHeight > 0) {
            $scrollOffset = $navHeight + 10;
        }

        if ($headerArea) {
            $bodyContainerTop = 0;
            $navBgColor = 'transparent';
        }

        if (isset($showHeader) && $showHeader) {
            $bodyContainerTop = 0;
            $navBgColor = 'transparent';
        }

        return [
            /* general */
            'scrollOffset' => $scrollOffset,
            'bodyContainerTop' => $bodyContainerTop,

            /* colors */
            'colorPrimary' => $colorMain,
            'btnTextColor' => $btnTextColor,

            /* typography */
            'bodyFontSize' => $bodyFontSize,
            'bodyColor' => $bodyColor,
            'headingColor' => $headingColor,
            'bodyFontWeight' => $bodyFontWeight,
            'headingFontWeight' => $headingFontWeight,

            /* nav */
            'navPosition' => $navPositionCSS,
            'navLinkColor' => $navBarFontColor,
            'navBgColor' => $navBgColor,
            'navBgColorScrolled' => $navBgColorScrolled,
            'navHeight' => $navHeight,
            'navMobileBgColor' => $navMobileBgColor,
            'navMobileTextColor' => $navMobileTextColor,

            /* page header */
            'pageHeaderMinHeightDesktop' => $pageHeaderMinHeightDesktop, /* todo */
            'pageHeaderMinHeightMobile' => $pageHeaderMinHeightMobile, /* todo */
            'pageHeaderImgPosition' => $pageHeaderImgPosition,
            'pageHeaderTextAlignment' => $pageHeaderTextAlignment, /* todo */
            'pageHeaderHeadingColor' => $pageHeaderHeadingColor, /* todo */
            'pageHeaderTextColor' => $pageHeaderTextColor, /* todo */

            /* footer */
            'footerBgColor' => $footerBgColor,
            'footerTextColor' => $footerTextColor,
            'footerLinkColor' => $footerLinkColor, /* todo as setting */
        ];
    }
}
