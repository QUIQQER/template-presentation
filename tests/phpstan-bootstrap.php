<?php

if (!defined('QUIQQER_SYSTEM')) {
    define('QUIQQER_SYSTEM', true);
}

if (!defined('QUIQQER_AJAX')) {
    define('QUIQQER_AJAX', true);
}

putenv("QUIQQER_OTHER_AUTOLOADERS=KEEP");

require_once __DIR__ . '/../../../../bootstrap.php';
require_once __DIR__ . '/stubs/QUI/AI/MCP/Skill/SkillProviderInterface.php';
require_once __DIR__ . '/stubs/QUI/AI/MCP/Skill/SkillRepository.php';
