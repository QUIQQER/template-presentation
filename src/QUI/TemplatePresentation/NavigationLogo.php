<?php

declare(strict_types=1);

namespace QUI\TemplatePresentation;

use QUI\Interfaces\Projects\Site;
use QUI\Projects\Media\Image;
use QUI\Projects\Media\Utils as MediaUtils;
use QUI\Projects\Project;

/** Logo variants belong to the website navigation, not the global project logo API. */
final class NavigationLogo
{
    /**
     * @param null|callable(string): Image $resolveImage
     * @return array{standard: ?Image, dark: ?Image, transparent: ?Image}
     */
    public static function getImages(
        Project $Project,
        Site $Site,
        bool $transparent,
        ?callable $resolveImage = null
    ): array {
        $resolveImage ??= MediaUtils::getImageByUrl(...);
        $Transparent = null;

        if ($transparent) {
            $Transparent = self::resolve(
                $Site->getAttribute('templatePresentation.nav.initialTransparent.logo'),
                $resolveImage
            );
            $Transparent ??= self::resolve(
                $Project->getConfig('templatePresentation.settings.navBarInitialTransparentLogo'),
                $resolveImage
            );
        }

        return [
            'standard' => $Project->getMedia()->getLogoImage(),
            'dark' => self::resolve($Project->getConfig('templatePresentation.settings.logoDark'), $resolveImage),
            'transparent' => $Transparent
        ];
    }

    /** @param callable(string): Image $resolveImage */
    private static function resolve(mixed $value, callable $resolveImage): ?Image
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return $resolveImage($value);
        } catch (\QUI\Exception) {
            return null;
        }
    }
}
