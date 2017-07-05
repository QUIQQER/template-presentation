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

    // social
    $socialNav    = '';
    $socialFooter = '';

    if ($Project->getConfig('templatePresentation.settings.social.show.nav')
        || $Project->getConfig('templatePresentation.settings.social.show.footer')
    ) {
        $socialHTML = '';

        // check what socials should be shown
        if ($Project->getConfig('templatePresentation.settings.social.facebook')) {
            $socialHTML .= '<a href="http://' .
                           $Project->getConfig('templatePresentation.settings.social.facebook')
                           . '"><span class="fa fa-facebook"></span></a>';
        }
        if ($Project->getConfig('templatePresentation.settings.social.twitter')) {
            $socialHTML .= '<a href="' .
                           $Project->getConfig('templatePresentation.settings.social.twitter')
                           . '"><span class="fa fa-twitter"></span></a>';
        }
        if ($Project->getConfig('templatePresentation.settings.social.google')) {
            $socialHTML .= '<a href="' .
                           $Project->getConfig('templatePresentation.settings.social.google')
                           . '"><span class="fa fa-google-plus"></span></a>';
        }
        if ($Project->getConfig('templatePresentation.settings.social.youtube')) {
            $socialHTML .= '<a href="' .
                           $Project->getConfig('templatePresentation.settings.social.youtube')
                           . '"><span class="fa fa-youtube-play"></span></a>';
        }
        if ($Project->getConfig('templatePresentation.settings.social.github')) {
            $socialHTML .= '<a href="' .
                           $Project->getConfig('templatePresentation.settings.social.github')
                           . '"><span class="fa fa-github"></span></a>';
        }

        // prepare social for nav
        if ($Project->getConfig('templatePresentation.settings.social.show.nav')) {
            $socialNav .= '<div class="header-bar-social">';
            $socialNav .= $socialHTML;
            $socialNav .= '</div>';
        }

        // prepare social for footer
        if ($Project->getConfig('templatePresentation.settings.social.show.footer')) {
            $socialFooter .= '<div class="footer-bar-social">';
            $socialFooter .= $socialHTML;
            $socialFooter .= '</div>';
        }
    }

    $MegaMenu->appendHTML(
        $socialNav .
        '<div class="header-bar-suggestSearch hide-on-mobile">
                    <input type="search" data-qui="package/quiqqer/search/bin/controls/Suggest" 
                    placeholder="' . $Locale->get('quiqqer/template-presentation', 'navbar.search.text') . '"/>
                    <span class="fa fa-fw fa-search"></span>
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
$templateSettings = QUI\TemplatePresentation\Utils::getConfig(array(
    'headerArea' => $headerArea,
    'Project'    => $Project,
    'Site'       => $Site,
    'Template'   => $Template
));

$templateSettings['BricksManager'] = $BricksManager;
$templateSettings['Breadcrumb']    = $Breadcrumb;
$templateSettings['MegaMenu']      = $MegaMenu;

/**
 * header: title
 */
/*$siteTitle = false;

if ($Site->getAttribute('templatePresentation.showTitle')) {
    $siteTitle = $Site->getAttribute('title');

    if ($Site->getAttribute('templatePresentation.altTitle') != '') {
        $siteTitle = $Site->getAttribute('templatePresentation.altTitle');
    }
}*/

/**
 * header short
 */
/*$siteShort = false;

if ($Site->getAttribute('templatePresentation.showShort')) {
    $siteShort = $Site->getAttribute('short');

    if ($Site->getAttribute('templatePresentation.altShort') != '') {
        $siteShort = $Site->getAttribute('templatePresentation.altShort');
    }
}*/



$Engine->assign(array(
//    'siteTitle'    => $siteTitle,
//    'siteShort'    => $siteShort,
    'socialFooter' => $socialFooter
));

$Engine->assign($templateSettings);
