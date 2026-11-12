<?php

declare(strict_types=1);

namespace QUITests\TemplatePresentation;

use PHPUnit\Framework\TestCase;
use QUI\Interfaces\Projects\Site;
use QUI\Projects\Media;
use QUI\Projects\Media\Image;
use QUI\Projects\Project;
use QUI\TemplatePresentation\NavigationLogo;

final class NavigationLogoTest extends TestCase
{
    public function testVariantPriorityAndInvalidImageFallbacks(): void
    {
        $Standard = $this->createMock(Image::class);
        $Dark = $this->createMock(Image::class);
        $Template = $this->createMock(Image::class);
        $Page = $this->createMock(Image::class);
        $Media = $this->createMock(Media::class);
        $Media->method('getLogoImage')->willReturn($Standard);

        $darkUrl = 'image.php?project=logo-test&id=1';
        $templateUrl = 'image.php?project=logo-test&id=2';
        $pageUrl = 'image.php?project=logo-test&id=3';
        $images = [$darkUrl => $Dark, $templateUrl => $Template, $pageUrl => $Page];

        $cases = [
            'page priority' => [true, $darkUrl, $pageUrl, $templateUrl, $Dark, $Page, [$pageUrl, $darkUrl]],
            'invalid page fallback' => [
                true, $darkUrl, 'invalid', $templateUrl, $Dark, $Template, ['invalid', $templateUrl, $darkUrl]
            ],
            'template fallback' => [true, '', '', $templateUrl, null, $Template, [$templateUrl]],
            'opaque navigation' => [false, $darkUrl, $pageUrl, $templateUrl, $Dark, null, [$darkUrl]],
            'invalid dark image' => [false, 'invalid', '', $templateUrl, null, null, ['invalid']],
            'invalid config type' => [false, ['invalid'], '', $templateUrl, null, null, []],
            'invalid transparent images' => [
                true, $darkUrl, 'invalid', 'invalid', $Dark, null, ['invalid', 'invalid', $darkUrl]
            ],
            'no variants' => [true, '', '', '', null, null, []]
        ];

        foreach ($cases as $name => $case) {
            [$transparent, $dark, $page, $template, $ExpectedDark, $ExpectedTransparent, $expectedUrls] = $case;
            $resolvedUrls = [];
            $resolveImage = static function (string $url) use ($images, &$resolvedUrls): Image {
                $resolvedUrls[] = $url;

                return $images[$url] ?? throw new \QUI\Exception('Image not found');
            };
            $Project = $this->createMock(Project::class);
            $Project->method('getMedia')->willReturn($Media);
            $Project->method('getConfig')->willReturnMap([
                ['templatePresentation.settings.logoDark', $dark],
                ['templatePresentation.settings.navBarInitialTransparentLogo', $template]
            ]);
            $Site = $this->createMock(Site::class);
            $Site->method('getAttribute')->willReturn($page);

            self::assertSame([
                'standard' => $Standard,
                'dark' => $ExpectedDark,
                'transparent' => $ExpectedTransparent
            ], NavigationLogo::getImages($Project, $Site, $transparent, $resolveImage), $name);
            self::assertSame($expectedUrls, $resolvedUrls, $name);
        }
    }
}
