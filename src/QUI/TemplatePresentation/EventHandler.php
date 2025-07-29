<?php

/**
 * This file contains \QUI\TemplatePresentation\EventHandler
 */

namespace QUI\TemplatePresentation;

use QUI;
use Smarty;

/**
 * Event Class
 *
 * @author www.pcsg.de (Michael Danielczok)
 */
class EventHandler
{
    /**
     * Clear system cache on project save
     *
     * @return void
     */
    public static function onProjectConfigSave(): void
    {
        QUI\Cache\Manager::clear('quiqqer/templatePresentation');
    }

    /**
     * Clear system cache on site save
     */
    public static function onSiteSave(QUI\Interfaces\Projects\Site $Site): void
    {
        $Project = $Site->getProject();
        $cacheName = md5($Project->getName() . $Project->getLang() . $Site->getId());

        QUI\Cache\Manager::clear('quiqqer/templatePresentation/' . $cacheName);
    }

    /**
     * Event: on smarty init
     *
     * @param Smarty $Smarty
     * @return void
     */
    public static function onSmartyInit(Smarty $Smarty): void
    {
        $Smarty->registerClass('QUI\TemplatePresentation\Utils', '\QUI\TemplatePresentation\Utils');
    }
}
