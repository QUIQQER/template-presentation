<?php

$Convert = new \QUI\Utils\Convert();

$navBarMainColor      = '#2d4d88';
$navBarFontColor      = '#ffffff';
$mobileFontColor      = '#ffffff';
$mobileMenuBackground = '#252122';

if ($Project->getConfig('templatePresentation.settings.navBarMainColor')) {
    $navBarMainColor = $Project->getConfig('templatePresentation.settings.navBarMainColor');
}

if ($Project->getConfig('templatePresentation.settings.navBarFontColor')) {
    $navBarFontColor = $Project->getConfig('templatePresentation.settings.navBarFontColor');
}

if ($Project->getConfig('templatePresentation.settings.mobileFontColor')) {
    $mobileFontColor = $Project->getConfig('templatePresentation.settings.mobileFontColor');
}

if ($Project->getConfig('templatePresentation.settings.mobileMenuBackground')) {
    $mobileMenuBackground = $Project->getConfig('templatePresentation.settings.mobileMenuBackground');
}

$navBarMainColorLighter = $Convert->colorBrightness($navBarMainColor, 0.9);

/**
 * colors
 */

$colorFooterBackground = '#414141';
$colorFooterFont       = '#D1D1D1';
$colorMain             = '#dd151b';
$buttonFontColor       = '#ffffff';
$colorMainContentFont  = '#5d5d5d';

if ($Project->getConfig('templatePresentation.settings.colorFooterBackground')) {
    $colorFooterBackground = $Project->getConfig('templatePresentation.settings.colorFooterBackground');
}

if ($Project->getConfig('templatePresentation.settings.colorFooterFont')) {
    $colorFooterFont = $Project->getConfig('templatePresentation.settings.colorFooterFont');
}

if ($Project->getConfig('templatePresentation.settings.colorMain')) {
    $colorMain = $Project->getConfig('templatePresentation.settings.colorMain');
}

if ($Project->getConfig('templatePresentation.settings.buttonFontColor')) {
    $buttonFontColor = $Project->getConfig('templatePresentation.settings.buttonFontColor');
}

if ($Project->getConfig('templatePresentation.settings.colorMainContentFont')) {
    $colorMainContentFont = $Project->getConfig('templatePresentation.settings.colorMainContentFont');
}

$navBarHeight = (int)$Project->getConfig('templatePresentation.settings.navBarHeight');
$headerHeightValue = (int)$Project->getConfig('templatePresentation.settings.headerHeightValue');
$bgColorSwitcherPrefix = $Project->getConfig('templatePresentation.settings.bgColorSwitcherPrefix');
$bgColorSwitcherSuffix = $Project->getConfig('templatePresentation.settings.bgColorSwitcherSuffix');
$headerImagePosition = $Project->getConfig('templatePresentation.settings.headerImagePosition');
$navPos = $Project->getConfig('templatePresentation.settings.navPos');
$colorMainButton = $Convert->colorBrightness($colorMain, 0.7);


ob_start();

?>
/**
 * Farbeinstellungen
 */

.header-bar,
.header-bar-inner a,
.header-bar-inner a:link,
.header-bar-inner a:active,
.header-bar-inner a:visited,
.header-bar-inner a:hover,
.quiqqer-menu-megaMenu-list-item-menu.control-background {
    color: <?php echo $navBarFontColor; ?>;
}

<?php if ($headerArea && $navPos == 'scroll') { ?>
.start-page .header-bar {
/*.start-page .header-bar-inner-nav {*/
    position: absolute;
}
<?php } ?>

<?php if (!$showHeader && !$headerArea) { ?>
.header-hidden .header-bar {
    background: <?php echo $navBarMainColor; ?>;
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.35);
}
<?php } ?>

/**
 * wenn Emotion / header angezeigt wird aber nav nicht fix ist,
 * dann trotzdem kein Abstnad der Seite von oben
 */
<?php if ($showHeader) {?>
.header-displayed .body-container {
    top: 0;
}
.header-bar {
    box-shadow: none;
}
<?php }; ?>

.header-bar-scrolled,
.header-bar-scrolled {
    background: <?php echo $navBarMainColor; ?>;
}


/* mobile nav background */
.slideout-menu .page-menu {
    background: <?php echo $mobileMenuBackground; ?>;
}

.slideout-menu .page-navigation-home,
.slideout-menu .left-menu-a,
.slideout-menu .page-menu-close {
    color: <?php echo $mobileFontColor; ?>;
}

.page-header-navigation-sub-list,
.page-header-navigation li:hover,
.header-bar-search:hover,
.quiqqer-menu-megaMenu-list-item-menu.control-background
    /*.quiqqer-menu-megaMenu-list-item:hover*/ {
    background: <?php echo $navBarMainColorLighter; ?>;
}

.quiqqer-menu-megaMenu-children-simple:after {
    border-bottom: 14px solid <?php echo $navBarMainColorLighter; ?>;
}

.quiqqer-menu-megaMenu-list-item:hover {
    background: rgba(255,255,255,0.15);
}

.color-main {
    color: <?php echo $colorMain; ?>;
}

.qui-sitetypes-sitemap-block-category .control-background {
    background: <?php echo $colorMain; ?>;
}

.header-bar,
.header-bar-inner-nav,
.page-header-navigation-entry,
.header-bar-search,
.header-bar-search:before,
.page-header-navigation-entry:before,
.header-bar-inner-logo {
    height: <?php echo $navBarHeight; ?>px;
}

.header-bar-search,
.page-header-navigation-entry,
.fa-chevron-down-mobile,
.quiqqer-menu-megaMenu-list-item,
.hide-on-desktop .quiqqer-menu-megaMenu-mobile,
.quiqqer-menu-megaMenu-mobile-search,
.header-bar-suggestSearch,
.fa.close-social-share,
.fa.open-social-share,
.quiqqer-bricks-languageswitch-dropdown {
    line-height: <?php echo $navBarHeight; ?>px;
}

.quiqqer-fa-levels-icon:hover,
.quiqqer-navigation-level-1 a:hover,
a.quiqqer-navigation-home:hover {
    color: <?php echo $colorMain; ?>;
}

.quiqqer-navigation-entry:hover .quiqqer-fa-list-icon {
    color: <?php echo $colorMain; ?>;
}

.control-color,
.mainColor,
.mainColorHover:hover,
.template-breadcrumb .quiqqer-breadcrumb ul li:last-child a span:last-child {
    color: <?php echo $colorMain; ?>;
}

.tpl-presentation-row a.qui-tags-tag:hover {
    border: 1px solid <?php echo $colorMain; ?>;
    color: <?php echo $colorMain; ?>;
}

#page input[type='checkbox']:checked + label::before,
#page input[type='radio']:checked + label::before,
.pace .pace-progress {
    background-color: <?php echo $colorMain; ?>;
}

input[type='submit'],
input[type='reset'],
input[type='button'],
button,
.button,
/*.tpl-presentation-row .button,*/
button:disabled,
button:disabled:hover,
a.template-button,
button.qui-button-active,
button.qui-button:active,
button.qui-button:hover {
    background-color: <?php echo $colorMain; ?>;
    color: <?php echo $buttonFontColor; ?>;
    border: 2px solid <?php echo $colorMain; ?>;
}

a.template-button:hover {
    background-color: <?php echo $colorMainButton; ?>;
}

.button__ghost__color {
    color: <?php echo $colorMain; ?>;
    border: 2px solid <?php echo $colorMain; ?>;
}

body,
.mainFontColor {
    color: <?php echo $colorMainContentFont; ?> !important;
}

textarea:hover,
textarea:focus,
input:hover,
input:focus,
select:hover,
select:focus {
    border-color: <?php echo $colorMain; ?>;
}

a,
a.link-simple-color,
a.slide-up-color {
    color: <?php echo $colorMain; ?>;
}

a.link-slide-up-color::before {
    background: <?php echo $colorMain; ?>;

}

.quiqqer-content a:hover:after {
    color: <?php echo $colorMain; ?>;
}

input[type='submit']:hover,
input[type='reset']:hover,
input[type='button']:hover,
button:hover,
.button-active,
.button:active,
.button:hover {
    color: <?php echo $colorMain; ?>;
}

.page-footer button {
    background: <?php echo $colorMain; ?>;
    color: #ffffff;
}

.page-footer {
    background: <?php echo $colorFooterBackground; ?>;
    color: <?php echo $colorFooterFont; ?> !important;
}

.page-footer h1,
.page-footer h2,
.page-footer h3,
.page-footer h4 {
    color: <?php echo $colorFooterFont; ?>;
}

.page-footer a:hover {
    color: <?php echo $colorMain; ?>;
}

.page-footer-copyright {
    border-top: 1px solid <?php echo $Convert->colorBrightness($colorFooterBackground, 0.9)?>
}

/* pagination */
.quiqqer-sheets-desktop a:hover {
    border: 1px solid <?php echo $colorMain; ?> !important;
    background-color: <?php echo $colorMain; ?>;
}

.quiqqer-sheets-desktop-limits a:hover {
    color: <?php echo $colorMain; ?>;
}

.control-background-active {
    background: <?php echo $colorMain; ?> !important;
    color: #FFFFFF !important;
}

.control-background {
    background: <?php echo $colorMain; ?>;
}

/**
 * background color prefix suffix switcher
 * Prefix
 */
<?php if ($bgColorSwitcherPrefix == 'display') { ?>
.brick-even-prefix {
    background: #f5f5f5;
}

.brick-odd-prefix {
    background: #e5e5e5;
}

<?php }; ?>

/**
 * background color prefix suffix switcher
 * Suffix
 */
<?php if ($bgColorSwitcherSuffix == 'display') { ?>
.brick-even-suffix {
    background: #f5f5f5;
}

.brick-odd-suffix {
    background: #e5e5e5;
}

<?php }; ?>

.page-header {
    min-height: <?php echo $headerHeightValue; ?>px;
    background-position: <?php echo $headerImagePosition; ?>;
    padding: calc(<?php echo $navBarHeight; ?>px + 20px) 0 calc(<?php echo $navBarHeight; ?>px - 20px);
}

/**
 * Menüposition
 */
<?php  if ($navPos == 'fix') { ?>
.header-bar {
    position: fixed !important;
}

/* if nav pos fix, then put the content down */
.body-container {
    top: <?php echo $navBarHeight; ?>px;
}

<?php }; ?>

<?php if ($headerArea) { ?>
.start-page .body-container {
    top: 0 !important;
}

<?php }; ?>

@media screen and (max-width: 767px) {
    .mobile-bar-social {
        background: <?php echo $Convert->colorBrightness($mobileMenuBackground, 0.9) ?>;
    }

    .mobile-bar-social-container a,
    .mobile-bar-social-title {
        color: <?php echo $mobileFontColor ?>;
    }
}

<?php

$settingsCSS = ob_get_contents();
ob_end_clean();

return $settingsCSS;

?>
