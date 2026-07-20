<?php

namespace QUI\TemplatePresentation\MCP;

use PHPUnit\Framework\TestCase;
use QUI\AI\MCP\Skill\SkillRepository;

class SkillProviderTest extends TestCase
{
    public function testFrontendConventionsSkillIsRegistered(): void
    {
        if (!method_exists(SkillRepository::class, 'get')) {
            self::markTestSkipped('The quiqqer/ai-mcp skill repository is not installed.');
        }

        $Repository = new SkillRepository();
        $Provider = new SkillProvider();
        $Provider->registerSkills($Repository);

        $Skill = $Repository->get('quiqqer_template_presentation_frontend_conventions');

        $this->assertNotNull($Skill);
        $this->assertSame('developer', $Skill->getCategory()->value);
        $this->assertStringContainsString('--qui-colors-primary', $Skill->getContent());
        $this->assertStringContainsString('quiqqer_template_presentation_css_classes', $Skill->getContent());
    }

    public function testCssClassesSkillIsRegistered(): void
    {
        if (!method_exists(SkillRepository::class, 'get')) {
            self::markTestSkipped('The quiqqer/ai-mcp skill repository is not installed.');
        }

        $Repository = new SkillRepository();
        $Provider = new SkillProvider();
        $Provider->registerSkills($Repository);

        $Skill = $Repository->get('quiqqer_template_presentation_css_classes');

        $this->assertNotNull($Skill);
        $this->assertSame('content', $Skill->getCategory()->value);
        $this->assertStringContainsString('.btn-primary', $Skill->getContent());
        $this->assertStringContainsString('.mt-0', $Skill->getContent());
    }
}
