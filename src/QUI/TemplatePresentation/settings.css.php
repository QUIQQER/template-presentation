<?php

/**
 * @var QUI\Projects\Project $Project
 * @var QUI\Projects\Site $Site
 * @var QUI\Interfaces\Template\EngineInterface $Engine
 * @var QUI\Template $Template
 **/

use QUI\Utils\Convert;

$Convert = new Convert();

$navBarBackground      = '#2d4d88';
$navBarFontColor      = '#ffffff';
$mobileFontColor      = '#ffffff';
$mobileMenuBackground = '#252122';

if ($Project->getConfig('templatePresentation.settings.navBarMainColor')) {
    $navBarBackground = $Project->getConfig('templatePresentation.settings.navBarMainColor');
}

$navBarScrolledBackground = $navBarBackground;

if ($Project->getConfig('templatePresentation.settings.navBarFontColor')) {
    $navBarFontColor = $Project->getConfig('templatePresentation.settings.navBarFontColor');
}

if ($Project->getConfig('templatePresentation.settings.mobileFontColor')) {
    $mobileFontColor = $Project->getConfig('templatePresentation.settings.mobileFontColor');
}

if ($Project->getConfig('templatePresentation.settings.mobileMenuBackground')) {
    $mobileMenuBackground = $Project->getConfig('templatePresentation.settings.mobileMenuBackground');
}

$navBarBackgroundLighter = $Convert->colorBrightness($navBarBackground, 0.9);

/**
 * colors
 */

$colorFooterBackground = '#414141';
$colorFooterFont       = '#D1D1D1';
$colorMain             = '#2e4d87';
$buttonFontColor       = '#ffffff';
$colorMainContentFont  = '#3b3b3a';
$colorMuted            = '#9095a4';

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
    $colorMuted            = $Convert->colorBrightness($colorMainContentFont, 0.6);
}

$navBarHeight = (int)$Project->getConfig('templatePresentation.settings.navBarHeight');
$headerHeightValue = (int)$Project->getConfig('templatePresentation.settings.headerHeightValue');
$headerImagePosition = $Project->getConfig('templatePresentation.settings.headerImagePosition');
$colorMainButton = $Convert->colorBrightness($colorMain, 0.7);


$navPos = $Project->getConfig('templatePresentation.settings.navPos');
$navCSSPos = 'absolute';
$bodyContainerTop = $navBarHeight;

if ($navPos == 'fix') {
    $navCSSPos = 'fixed';
}

$scrollOffset = 0;
if ($navPos == 'fix' && $navBarHeight > 0) {
    $scrollOffset = $navBarHeight + 10;
}

if (isset($headerArea) && $headerArea) {
    $bodyContainerTop = 0;
    $navBarBackground = 'transparent';
}

if (isset($showHeader) && $showHeader) {
    $bodyContainerTop = 0;
    $navBarBackground = 'transparent';
}

ob_start();

?>

:root {
    --scroll-padding: <?php echo $scrollOffset;?>px;

    --_qui-color-main: var(--qui-color-main, <?php echo $colorMain;?>);
    --_qui-color-primary: var(--qui-color-primary, <?php echo $colorMain;?>);
    --_qui-color-body: var(--qui-color-body, <?php echo $colorMainContentFont;?>);
    --_qui-color-muted: var(--qui-color-muted, <?php echo $colorMuted;?>);

    --_qui-btn-primary-color: var(--qui-btn-primary-color, <?php echo $buttonFontColor;?>);

    --qui-nav-color: <?php echo $navBarFontColor;?>;
    --qui-nav-position: <?php echo $navCSSPos;?>;
    --qui-nav-bg: <?php echo $navBarBackground;?>;
    --qui-nav-bg-lighter: <?php echo $navBarBackgroundLighter;?>;
    --qui-nav-height: <?php echo $navBarHeight; ?>px;
    --qui-nav-scrolled-bg: <?php echo $navBarScrolledBackground;?>;
    --qui-body-container-top: <?php echo $bodyContainerTop;?>px;
    --qui-nav-mobile-bg: <?php echo $mobileMenuBackground; ?>;
    --qui-nav-mobile-font-color: <?php echo $mobileFontColor; ?>;
    --qui-nav-mobile-social-bar-bg: <?php echo $Convert->colorBrightness($mobileMenuBackground, 0.9)?>;

    --qui-header-min-height: <?php echo $headerHeightValue;?>px;
    --qui-header-img-position: <?php echo $headerImagePosition;?>;

    --qui-footer-bg: <?php echo $colorFooterBackground;?>;
    --qui-footer-font-color: <?php echo $colorFooterFont;?>;
    --qui-footer-copyrigth-border-top-color: <?php echo $Convert->colorBrightness($colorFooterBackground, 0.9);?>;
}

<?php

$settingsCSS = ob_get_contents();
ob_end_clean();

return $settingsCSS;

?>
