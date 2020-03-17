<?php
/**
 * This file contains \QUI\TemplatePresentation\EventHandler
 */

namespace QUI\TemplatePresentation;

use QUI;

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
    public static function onProjectConfigSave()
    {
        try {
            QUI\Cache\Manager::clear('quiqqer/templatePresentation');
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }
    }

    /**
     * Clear system cache on site save
     *
     * @return void
     */
    public static function onSiteSave($Site)
    {
        $Project = $Site->getProject();
        $cacheName = md5($Site->getId() . $Project->getName() . $Project->getLang());

        try {
            QUI\Cache\Manager::clear('quiqqer/templatePresentation/' . $cacheName);
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }
    }
}
