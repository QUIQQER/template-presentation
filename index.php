<?php

/**
 * Emotion
 */
QUI\Utils\Site::setRecursivAttribute($Site, 'image_emotion');

// Inhalts Verhalten
if ($Site->getAttribute('templatePresentation.showTitle') ||
    $Site->getAttribute('templatePresentation.showShort')
) {
    $Template->setAttribute('content-header', false);
}

/**
 * Mega menu
 */
$MegaMenu = new QUI\Menu\MegaMenu(array(
    'showStart' => false
));

$searchMobile = '';

$types = array(
    'quiqqer/sitetypes:types/search',
    'quiqqer/search:types/search'
);

$searchSites = $Project->getSites(array(
    'where' => array(
        'type' => array(
            'type'  => 'IN',
            'value' => $types
        )
    ),
    'limit' => 1
));

if (count($searchSites)) {
    try {
        $searchUrl = $searchSites[0]->getUrlRewritten();

        $searchMobile = '<div class="quiqqer-menu-megaMenu-mobile-search hide-on-desktop"
                                  style="width: auto; font-size: 30px !important;">
                    <a href="' . $searchUrl . '"
                    class="header-bar-search-link searchMobile">
                        <i class="fa fa-search header-bar-search-icon"></i>
                    </a>
                </div>';
    } catch (QUI\Exception $Exception) {
        QUI\System\Log::addNotice($Exception->getMessage());
    }
}

$alt = "";
if ($Project->getMedia()->getLogoImage()) {
    $alt = $Project->getMedia()->getLogoImage()->getAttribute('title');
}

$MegaMenu->prependHTML(
    '<div class="header-bar-inner-logo">
                <a href="' . URL_DIR . '" class="page-header-logo">
                <img src="' . $Project->getMedia()->getLogo() . '" alt="' . $alt . '"/></a>
            </div>'
);

try {
    QUI::getPackage('quiqqer/search');
//    $Locale = QUI::getLocale();

    $MegaMenu->appendHTML(
        '<div class="header-bar-suggestSearch hide-on-mobile">
                    <input type="search" data-qui="package/quiqqer/search/bin/controls/Suggest" 
                    placeholder="' . $Locale->get('quiqqer/template-presentation', 'navbar.search.text') . '"/>
                </div>' .
        $searchMobile
    );
} catch (QUI\Exception $Exception) {
    QUI\System\Log::addNotice($Exception->getMessage());
}


/**
 * Breadcrumb
 */
$Breadcrumb = new QUI\Controls\Breadcrumb();

/**
 * Template config
 */
$templateSettings = QUI\TemplatePresentation\Utils::getConfig(array(
    'Project'  => $Project,
    'Site'     => $Site,
    'Template' => $Template
));

$templateSettings['BricksManager'] = \QUI\Bricks\Manager::init();
$templateSettings['Breadcrumb']    = $Breadcrumb;
$templateSettings['MegaMenu']      = $MegaMenu;


$Engine->assign($templateSettings);
