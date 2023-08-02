<?php
$Locale = QUI::getLocale();

/**
 * header area on start page?
 */
$headerArea    = false;
$BricksManager = \QUI\Bricks\Manager::init();
if (count($BricksManager->getBricksByArea('header', $Site)) > 0) {
    $headerArea = true;
}

/**
 * Template config
 */
$templateSettings = QUI\TemplatePresentation\Utils::getConfig([
    'headerArea' => $headerArea,
    'Project'    => $Project,
    'Site'       => $Site,
    'Template'   => $Template
]);

/**
 * Set emotion and independent menu recursive
 */
QUI\Utils\Site::setRecursiveAttribute($Site, 'image_emotion');
QUI\Utils\Site::setRecursiveAttribute($Site, 'templatePresentation.independentMenuId');

// Inhalts Verhalten
if ($Site->getAttribute('templatePresentation.showTitle') || $Site->getAttribute('templatePresentation.showShort')) {
    $Template->setAttribute('content-header', false);
}

/**
 * Mega menu
 */
$params = [
    'showStart' => false
];

if ($Project->getConfig('templatePresentation.settings.enableIndependentMenu') && $Project->getConfig('templatePresentation.settings.menuId')) {
    $params['menuId']              = $Project->getConfig('templatePresentation.settings.menuId');
    $params['showFirstLevelIcons'] = $Project->getConfig('templatePresentation.settings.showFirstLevelIcons');
}

// Site own / independent menu
if ($Site->getAttribute('templatePresentation.independentMenuId')) {
    $params['menuId'] = $Site->getAttribute('templatePresentation.independentMenuId');
}

$MegaMenu = new QUI\Menu\MegaMenu($params);

/**
 * search
 */
$search      = '';
$dataQui     = '';
$noSearch    = 'no-search';
$inputSearch = '';
/* search setting is on? */
if ($Project->getConfig('templatePresentation.settings.search') != 'hide') {
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
        $dataQui = 'data-qui="package/quiqqer/search/bin/controls/Suggest"';
    }

    $searchSites = $Project->getSites([
        'where' => [
            'type' => [
                'type'  => 'IN',
                'value' => $types
            ]
        ],
        'limit' => 1
    ]);

    if (count($searchSites)) {
        try {
            $noSearch   = '';
            $searchUrl  = $searchSites[0]->getUrlRewritten();
            $searchForm = '';

            switch ($Project->getConfig('templatePresentation.settings.search')) {
                case 'input':
                    $searchForm  = '
                    <form  action="'.$searchUrl.'" class="header-bar-suggestSearch header-bar-suggestSearch--type-input hide-on-mobile" method="get">
                        <input type="search" name="search" 
                                class="only-input" '.$dataQui.'
                                placeholder="'
                        .$Locale->get('quiqqer/template-presentation', 'navbar.search.text').
                        '"/>
                    </form>';
                    $inputSearch = 'input-search';
                    break;
                case 'inputAndIcon':
                    $searchForm = '
                    <form  action="'.$searchUrl.'" class="header-bar-suggestSearch header-bar-suggestSearch--type-icon hide-on-mobile" method="get">
                        <div class="header-bar-suggestSearch-wrapper">
                            <input type="search" name="search"
                                    class="input-and-icon" '.$dataQui.' 
                                    placeholder="'
                        .$Locale->get('quiqqer/template-presentation', 'navbar.search.text').
                        '"/>
                        </div>
                        <span class="fa fa-fw fa-search"></span>
                    </form>';
                    break;

                case 'popup':
                    $searchForm = '<button>Suche</button>';
                    break;
            }

            $search = $searchForm.
                '<div class="quiqqer-menu-megaMenu-mobile-search"
                                  style="width: auto; font-size: 30px !important;">
                    <a href="'.$searchUrl.'"
                    class="header-bar-search-link searchMobile">
                        <i class="fa fa-search header-bar-search-icon"></i>
                    </a>
                </div>';
            //@michael dev         $searchForm = '<div  data-qui-searchUrl="' . $searchUrl . '" class="header-bar-search-typePopup hide-on-mobile"><button><span class="fa fa-search"></span> <span class="button-label">Suche</span></button></div>';


            $search = $searchForm.
                '<div class="quiqqer-menu-megaMenu-mobile-search"
                                  style="width: auto; font-size: 30px !important;">
                    <a href="'.$searchUrl.'"
                    class="header-bar-search-link searchMobile">
                        <i class="fa fa-search header-bar-search-icon"></i>
                    </a>
                </div>';
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::addNotice($Exception->getMessage());
        }
    }
}

/**
 * Dropdown Language switch
 */
$showDropDownFlag = false;
$DropDownFlag     = '';
$showFlags        = false;
$showText         = false;

switch ($Project->getConfig('templatePresentation.settings.dropdownLangNav')) {
    case 'flag':
        $showFlags        = true;
        $showDropDownFlag = true;
        break;

    case 'text':
        $showText         = true;
        $showDropDownFlag = true;
        break;

    case 'flagAndText':
        $showFlags        = true;
        $showText         = true;
        $showDropDownFlag = true;
        break;
}

if ($showDropDownFlag) {
    $DropDown = new QUI\Bricks\Controls\LanguageSwitches\DropDown([
        'Site'      => $Site,
        'showFlags' => $showFlags,
        'showText'  => $showText
    ]);

    $DropDownFlag = $DropDown->create();
}

$logoData             = $templateSettings['logoData'];
$widthCustomProperty  = '';
$heightCustomProperty = '';

if ($logoData['width'] !== false) {
    $widthCustomProperty = '--logo-width: '.$logoData['width'].'px;';
}

if ($logoData['height'] !== false) {
    $heightCustomProperty = '--logo-height: '.$logoData['height'].'px;';
}

$MegaMenu->prependHTML(
    '<div class="header-bar-inner-logo" 
        style="'.$widthCustomProperty.$heightCustomProperty.'">
        <a href="'.$Project->get(1)->getUrlRewritten().'" class="page-header-logo">
        <img src="'.$logoData['url'].'" alt="'.$logoData['alt'].'"
            height="'.$logoData['height'].'" width="'.$logoData['width'].'"
        </a>
    </div>'
);

// social
$social          = "false";
$socialNav       = '';
$socialFooter    = '';
$socialMobileNav = '';

if ($Project->getConfig('templatePresentation.settings.social.show.nav') || $Project->getConfig('templatePresentation.settings.social.show.footer')) {
    $social     = "true";
    $socialHTML = '';

    // check which socials should be displayed
    if ($Project->getConfig('templatePresentation.settings.social.facebook')) {
        $socialHTML .= '<a href="'.
            $Project->getConfig('templatePresentation.settings.social.facebook')
            .'" target="_blank"><span class="fa fa-facebook"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.twitter')) {
        $socialHTML .= '<a href="'.
            $Project->getConfig('templatePresentation.settings.social.twitter')
            .'" target="_blank"><span class="fa fa-twitter"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.google')) {
        $socialHTML .= '<a href="'.
            $Project->getConfig('templatePresentation.settings.social.google')
            .'" target="_blank"><span class="fa fa-google-plus"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.youtube')) {
        $socialHTML .= '<a href="'.
            $Project->getConfig('templatePresentation.settings.social.youtube')
            .'" target="_blank"><span class="fa fa-youtube-play"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.github')) {
        $socialHTML .= '<a href="'.
            $Project->getConfig('templatePresentation.settings.social.github')
            .'" target="_blank"><span class="fa fa-github"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.gitlab')) {
        $socialHTML .= '<a href="'.
            $Project->getConfig('templatePresentation.settings.social.gitlab')
            .'" target="_blank"><span class="fa fa-gitlab"></span></a>';
    }

    // prepare social for nav
    if ($Project->getConfig('templatePresentation.settings.social.show.nav')) {
        $socialNav .= '<div class="header-bar-social hide-on-mobile '.$noSearch.$inputSearch.'">';
        $socialNav .= $socialHTML;
        $socialNav .= '</div>';

        $socialMobileNav .= '<span class="mobile-bar-social-title">Social Media</span>';
        $socialMobileNav .= '<div class="mobile-bar-social-container">';
        $socialMobileNav .= $socialHTML;
        $socialMobileNav .= '</div>';
    }

    // prepare social for footer
    if ($Project->getConfig('templatePresentation.settings.social.show.footer')) {
        $socialFooter .= '<div class="footer-bar-social">';
        $socialFooter .= $socialHTML;
        $socialFooter .= '</div>';
    }
}

$MegaMenu->appendHTML(
    $search.$socialNav.$DropDownFlag
);


/**
 * Breadcrumb
 */
$Breadcrumb = new QUI\Controls\Breadcrumb();

$templateSettings['BricksManager']   = $BricksManager;
$templateSettings['Breadcrumb']      = $Breadcrumb;
$templateSettings['MegaMenu']        = $MegaMenu;
$templateSettings['social']          = $social;
$templateSettings['socialMobileNav'] = $socialMobileNav;

/**
 * body class
 */
$bodyClass = '';
$startPage = false;

switch ($Template->getLayoutType()) {
    case 'layout/startPage':
        $bodyClass = 'start-page';
        $startPage = true;
        break;

    case 'layout/noSidebar':
        $bodyClass = 'no-sidebar';
        break;

    case 'layout/noSidebarSmall':
        $bodyClass = 'no-sidebar-small';
        break;

    case 'layout/rightSidebar':
        $bodyClass = 'right-sidebar';
        break;

    case 'layout/leftSidebar':
        $bodyClass = 'left-sidebar';
        break;
}

$templateSettings['socialFooter'] = $socialFooter;
$templateSettings['bodyClass']    = $bodyClass;
$templateSettings['startPage']    = $startPage;

/**
 * Own object container for template settings
 * Smarty example: {$Template->getAttribute('TemplateSetting')->getAttribute('navPos')}
 */
$Setting = new \QUI\QDOM();
$Setting->setAttributes($templateSettings);
$Template->setAttribute('TemplateSetting', $Setting);

$Engine->assign($templateSettings);
