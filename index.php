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
QUI\Utils\Site::setRecursiveAttribute($Site, 'templatePresentation.independentMenuIdLoggedIn');

// Inhalts Verhalten
if ($Site->getAttribute('templatePresentation.showTitle') || $Site->getAttribute('templatePresentation.showShort')) {
    $Template->setAttribute('content-header', false);
}

/**
 * Mega menu
 */
$menuDropdownIcon = 'fa-solid fa-caret-down';
$SessionUser = QUI::getUserBySession();
$sessionUserIsAuth = QUI::getUsers()->isAuth($SessionUser);
$params = [
    'showStart' => false,
    'subMenuIndicator' => $menuDropdownIcon
];

// menu settings may be multilingual: JSON {lang: menuId}; a language without
// its own menu falls back to the default language. A plain menu id (old
// format) applies to all languages.
$resolveIndependentMenuId = static function ($value) use ($Project) {
    $languages = json_decode((string)$value, true);

    if (!is_array($languages)) {
        return $value;
    }

    $menuId = $languages[$Project->getLang()] ?? '';

    if (empty($menuId)) {
        $menuId = $languages[$Project->getAttribute('default_lang')] ?? '';
    }

    return $menuId;
};

$independentMenuId = $resolveIndependentMenuId(
    $Project->getConfig('templatePresentation.settings.menuId')
);

if (
    $Project->getConfig('templatePresentation.settings.enableIndependentMenu')
    && $independentMenuId
) {
    $params['menuId'] = $independentMenuId;
    $params['showFirstLevelIcons'] = $Project->getConfig('templatePresentation.settings.showFirstLevelIcons');
}

$independentMenuIdLoggedIn = $resolveIndependentMenuId(
    $Project->getConfig('templatePresentation.settings.menuIdLoggedIn')
);

if ($sessionUserIsAuth && $independentMenuIdLoggedIn) {
    $params['menuId'] = $independentMenuIdLoggedIn;
}

// Site own / independent menu
if ($Site->getAttribute('templatePresentation.independentMenuId')) {
    $params['menuId'] = $Site->getAttribute('templatePresentation.independentMenuId');
}

if (
    $sessionUserIsAuth
    && $Site->getAttribute('templatePresentation.independentMenuIdLoggedIn')
) {
    $params['menuId'] = $Site->getAttribute('templatePresentation.independentMenuIdLoggedIn');
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

if ($templateSettings['showLangSelectNav']) {
    $LangSelectControl = new QUI\Bricks\Controls\LanguageSwitches\DropDown([
        'Site' => $Site,
        'buttonShowFlag' => $templateSettings['showFlag'],
        'buttonText' => $templateSettings['showText'],
        'arrowIconCssClass' => $menuDropdownIcon
    ]);
}

$templateSettings['LangSelectControl'] = $LangSelectControl;

// footer variant: flat link list instead of a dropdown (a dropdown at the
// bottom of the page would open past the viewport edge)
$LangSelectFooterControl = null;

if ($templateSettings['showLangSelectFooter']) {
    $LangSelectFooterControl = new QUI\Bricks\Controls\LanguageSwitches\Flags([
        'Site' => $Site,
        'showFlags' => $templateSettings['showFlag'],
        'showText' => (bool)$templateSettings['showText']
    ]);
}

$templateSettings['LangSelectFooterControl'] = $LangSelectFooterControl;

/**
 * Breadcrumb
 */
$breadcrumbAttributes = $templateSettings['breadcrumb'];

if (isset($breadcrumbAttributes['showTitle'])) {
    $breadcrumbAttributes['showTitle'] = $breadcrumbAttributes['showTitle'] === 'enable';
}

if (isset($breadcrumbAttributes['titleText'])) {
    $breadcrumbTitleText = json_decode($breadcrumbAttributes['titleText'], true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($breadcrumbTitleText)) {
        $projectLang = $Project->getLang();
        $breadcrumbAttributes['titleText'] = $breadcrumbTitleText[$projectLang] ?? '';

        if ($breadcrumbAttributes['titleText'] === '') {
            unset($breadcrumbAttributes['titleText']);
        }
    } elseif (!$breadcrumbAttributes['titleText']) {
        unset($breadcrumbAttributes['titleText']);
    }
}

$Breadcrumb = new QUI\Controls\Breadcrumb($breadcrumbAttributes);

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

    case 'layout/fullWidth':
        $bodyClass = 'full-width';
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
$templateSettings['Site'] = $Site;

/**
 * Own object container for template settings
 * Smarty example: {$Template->getAttribute('TemplateSetting')->getAttribute('navPos')}
 */
$Setting = new \QUI\QDOM();
$Setting->setAttributes($templateSettings);
$Template->setAttribute('TemplateSetting', $Setting);

$Engine->assign($templateSettings);
