<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test of SkillLevelTranslation.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelTranslationTest extends KernelTestCase {

    /**
     * Test the implementation of the entity.
     *
     * @return void
     */
    public function testImplementation(): void {
        $entity = new SkillLevelTranslation();
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity", $entity, "1. The entity musts be an instance of [AbstractTranslationEntity].");
    }

    /**
     * Test bad object instance.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [SkillLevel] expected!
     *
     * @return void
     */
    public function testBadObjectInstance(): void {
        $skillLevelTranslation = new SkillLevelTranslation();
        $skillLevelTranslation->setTranslatable(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity());
    }

}
