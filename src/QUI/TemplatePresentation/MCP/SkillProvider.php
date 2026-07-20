<?php

/**
 * This file contains the \QUI\TemplatePresentation\MCP\SkillProvider
 */

namespace QUI\TemplatePresentation\MCP;

use QUI\AI\MCP\Skill\SkillProviderInterface;
use QUI\AI\MCP\Skill\SkillRepository;

/**
 * Template presentation MCP skill provider
 */
class SkillProvider implements SkillProviderInterface
{
    public function registerSkills(SkillRepository $repository): void
    {
        $root = dirname(__DIR__, 4);

        $repository->addFromMarkdownFile(
            $root . '/skills/developer/quiqqer_template_presentation_frontend_conventions.md'
        );

        $repository->addFromMarkdownFile(
            $root . '/skills/content/quiqqer_template_presentation_css_classes.md'
        );
    }
}
