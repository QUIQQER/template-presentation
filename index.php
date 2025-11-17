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
$menuDropdownIcon = 'fa-solid fa-caret-down';
$params = [
    'showStart' => false,
    'subMenuIndicator' => $menuDropdownIcon
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
 * Logo for transparent nav
 */
if ($templateSettings['logoForTransparentNav']) {
    try {
        $templateSettings['LogoForTransparentNav'] = QUI\Projects\Media\Utils::getImageByUrl(
            $templateSettings['logoForTransparentNav']
        );
    } catch (Exception $e) {
        QUI\System\Log::addError($e->getMessage());
    }
}

/**
 * Language select
 */
$LangSelectControl = null;

if ($templateSettings['showLangSelect']) {
    $LangSelectControl = new QUI\Bricks\Controls\LanguageSwitches\DropDown([
        'Site' => $Site,
        'buttonShowFlag' => $templateSettings['showFlag'],
        'buttonText' => $templateSettings['showText'],
        'arrowIconCssClass' => $menuDropdownIcon
    ]);
}

$templateSettings['LangSelectControl'] = $LangSelectControl;

/**
 * Breadcrumb
 */
$Breadcrumb = new QUI\Controls\Breadcrumb();

$templateSettings['BricksManager'] = $BricksManager;
$templateSettings['Breadcrumb'] = $Breadcrumb;
$templateSettings['MegaMenu'] = $MegaMenu;

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
