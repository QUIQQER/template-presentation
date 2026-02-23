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
     * Create CSS file with project settings
     *
     * @param string $projectName
     * @param array $config
     * @param array $params
     * @return void
     */
    public static function onProjectConfigSave(string $projectName, array $config, array $params): void
    {
        QUI\Cache\Manager::clear('quiqqer/templatePresentation');

        $cssVariableMap = [
            'templatePresentation.settings.colorMain' => '--template-settings__color-primary',
            'templatePresentation.settings.buttonFontColor' => '--template-settings__btn-text-color'
        ];

        $cssVariables = [];

        foreach ($cssVariableMap as $configKey => $cssVariable) {
            $value = '';

            if (isset($config[$configKey])) {
                $value = (string) $config[$configKey];
            } elseif (isset($params[$configKey])) {
                $value = (string) $params[$configKey];
            }

            $value = trim($value);

            if ($value === '') {
                continue;
            }

            if (!preg_match('/\A[#a-zA-Z0-9(),.%\s+-]+\z/', $value)) {
                continue;
            }

            $cssVariables[$cssVariable] = $value;
        }

        if (empty($cssVariables)) {
            return;
        }

        $cssContent = ":root {\n";

        foreach ($cssVariables as $cssVariable => $value) {
            $cssContent .= "    {$cssVariable}: {$value};\n";
        }

        $cssContent .= "}\n";

        $packageDir = dirname(__DIR__, 3);
        $cssFile = $packageDir . '/bin/css/project-settings.css';
        @file_put_contents($cssFile, $cssContent);
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
        if (!isset($Smarty->registered_classes['QUI\TemplatePresentation\Utils'])) {
            $Smarty->registerClass('QUI\TemplatePresentation\Utils', '\QUI\TemplatePresentation\Utils');
        };
    }
}
