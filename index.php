<?php

$Locale = QUI::getLocale();

/**
 * header area on start page?
 */
$headerArea = false;
$BricksManager = \QUI\Bricks\Manager::init();
if (count($BricksManager->getBricksByArea('header', $Site)) > 0) {
    $headerArea = true;
}

/**
 * Template config
 */
$templateSettings = QUI\TemplatePresentation\Utils::getConfig([
    'headerArea' => $headerArea,
    'Project' => $Project,
    'Site' => $Site,
    'Template' => $Template
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

if (
    $Project->getConfig('templatePresentation.settings.enableIndependentMenu')
    && $Project->getConfig('templatePresentation.settings.menuId')
) {
    $params['menuId'] = $Project->getConfig('templatePresentation.settings.menuId');
    $params['showFirstLevelIcons'] = $Project->getConfig('templatePresentation.settings.showFirstLevelIcons');
}

// Site own / independent menu
if ($Site->getAttribute('templatePresentation.independentMenuId')) {
    $params['menuId'] = $Site->getAttribute('templatePresentation.independentMenuId');
}

$MegaMenu = new QUI\Menu\MegaMenu($params);

/**
 * Language select
 */
$LangSelectControl = null;

if ($templateSettings['showLangSelect']) {
    $LangSelectControl = new QUI\Bricks\Controls\LanguageSwitches\DropDown([
        'Site' => $Site,
        'buttonShowFlag' => $templateSettings['showFlag'],
        'buttonText' => $templateSettings['showText']
    ]);
}

$templateSettings['LangSelectControl'] = $LangSelectControl;

// social
$social = "false";
$socialFooter = '';
$socialMobileNav = '';

if (
    $Project->getConfig('templatePresentation.settings.social.show.nav')
    || $Project->getConfig('templatePresentation.settings.social.show.footer')
) {
    $social = "true";
    $socialHTML = '';

    // check which socials should be displayed
    if ($Project->getConfig('templatePresentation.settings.social.facebook')) {
        $socialHTML .= '<a href="' .
            $Project->getConfig('templatePresentation.settings.social.facebook')
            . '" target="_blank"><span class="fa fa-facebook"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.twitter')) {
        $socialHTML .= '<a href="' .
            $Project->getConfig('templatePresentation.settings.social.twitter')
            . '" target="_blank"><span class="fa fa-twitter"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.google')) {
        $socialHTML .= '<a href="' .
            $Project->getConfig('templatePresentation.settings.social.google')
            . '" target="_blank"><span class="fa fa-google-plus"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.youtube')) {
        $socialHTML .= '<a href="' .
            $Project->getConfig('templatePresentation.settings.social.youtube')
            . '" target="_blank"><span class="fa fa-youtube-play"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.github')) {
        $socialHTML .= '<a href="' .
            $Project->getConfig('templatePresentation.settings.social.github')
            . '" target="_blank"><span class="fa fa-github"></span></a>';
    }
    if ($Project->getConfig('templatePresentation.settings.social.gitlab')) {
        $socialHTML .= '<a href="' .
            $Project->getConfig('templatePresentation.settings.social.gitlab')
            . '" target="_blank"><span class="fa fa-gitlab"></span></a>';
    }

    // prepare social for nav
    if ($Project->getConfig('templatePresentation.settings.social.show.nav')) {
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

/**
 * Breadcrumb
 */
$Breadcrumb = new QUI\Controls\Breadcrumb();

$templateSettings['BricksManager'] = $BricksManager;
$templateSettings['Breadcrumb'] = $Breadcrumb;
$templateSettings['MegaMenu'] = $MegaMenu;
$templateSettings['social'] = $social;
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
$templateSettings['bodyClass'] = $bodyClass;
$templateSettings['startPage'] = $startPage;

/**
 * Own object container for template settings
 * Smarty example: {$Template->getAttribute('TemplateSetting')->getAttribute('navPos')}
 */
$Setting = new \QUI\QDOM();
$Setting->setAttributes($templateSettings);
$Template->setAttribute('TemplateSetting', $Setting);

$Engine->assign($templateSettings);
