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


/**
 * check if quiqqer search plugin is installed
 */
$search = '';
if (QUI::getPackageManager()->isInstalled('quiqqer/search')) {

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

            $search = '<form  action="' . $searchUrl . '" class="header-bar-suggestSearch hide-on-mobile">
                    <input type="search" name="search" data-qui="package/quiqqer/search/bin/controls/Suggest" 
                    placeholder="' . $Locale->get('quiqqer/template-presentation', 'navbar.search.text') . '"/>
                    <span class="fa fa-fw fa-search"></span>
                </form>' .

                      '<div class="quiqqer-menu-megaMenu-mobile-search"
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


// social
$social          = false;
$socialNav       = '';
$socialFooter    = '';
$socialMobileNav = '';

if ($Project->getConfig('templatePresentation.settings.social.show.nav')
    || $Project->getConfig('templatePresentation.settings.social.show.footer')
) {
    $social     = true;
    $socialHTML = '';

    // check which socials should be displayed
    if ($Project->getConfig('templatePresentation.settings.social.facebook')) {
        $socialHTML .= '<a href="http://' .
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
        $socialNav .= '<div class="header-bar-social hide-on-mobile">';
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
    $socialNav . $search
);


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

$Engine->assign($templateSettings);
