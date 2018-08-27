<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test of ExperienceTranslation
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExperienceTranslationTest extends KernelTestCase {

    /**
     * Test the implementation of the entity.
     */
    public function testImplementation() {
        $entity = new ExperienceTranslation("fr", "description", "Description FR");
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity", $entity, "1. The entity musts be an instance of [AbstractTranslationEntity].");
        $this->assertInstanceOf("Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation", $entity, "2. The entity musts be an instance of [AbstractPersonalTranslation].");
    }

    /**
     * Test bad object instance.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [Experience] expected!
     */
    public function testBadObjectInstance() {
        new ExperienceTranslation("fr", "description", "Description FR", new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity());
    }

}
