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

    public function testCreateLandingpageSkillIsRegistered(): void
    {
        if (!method_exists(SkillRepository::class, 'get')) {
            self::markTestSkipped('The quiqqer/ai-mcp skill repository is not installed.');
        }

        $Repository = new SkillRepository();
        $Provider = new SkillProvider();
        $Provider->registerSkills($Repository);

        $Skill = $Repository->get('quiqqer_template_presentation_create_landingpage');

        $this->assertNotNull($Skill);
        $this->assertSame('content', $Skill->getCategory()->value);
        $this->assertStringContainsString('Decide Navigation And Footer', $Skill->getContent());
        $this->assertStringContainsString('Establish The Design Direction', $Skill->getContent());
        $this->assertStringContainsString('a representative existing page or a screenshot', $Skill->getContent());
        $this->assertStringContainsString('whether new images should be created', $Skill->getContent());
        $this->assertStringContainsString('quiqqer_template_presentation_landingpage_01', $Skill->getContent());
    }

    public function testLandingpageOneSkillIsRegistered(): void
    {
        if (!method_exists(SkillRepository::class, 'get')) {
            self::markTestSkipped('The quiqqer/ai-mcp skill repository is not installed.');
        }

        $Repository = new SkillRepository();
        $Provider = new SkillProvider();
        $Provider->registerSkills($Repository);

        $Skill = $Repository->get('quiqqer_template_presentation_landingpage_01');

        $this->assertNotNull($Skill);
        $this->assertSame('content', $Skill->getCategory()->value);
        $this->assertStringContainsString('Required Section Order', $Skill->getContent());
        $this->assertStringContainsString('CustomerReviewsFlow', $Skill->getContent());
        $this->assertStringContainsString('Musterkunde', $Skill->getContent());
        $this->assertStringContainsString('Never create an empty review flow', $Skill->getContent());
    }
}
