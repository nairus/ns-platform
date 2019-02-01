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
     * @var ExperienceTranslation
     */
    private $object;

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new ExperienceTranslation();
    }

    /**
     * Test the implementation of the entity.
     *
     * @return void
     */
    public function testImplementation(): void {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity", $this->object, "1. The entity musts be an instance of [AbstractTranslationEntity].");
    }

    /**
     * Test bad object instance.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [Experience] expected!
     *
     * @return void
     */
    public function testBadObjectInstance(): void {
        $this->object->setTranslatable(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity());
    }

    /**
     * Test the getter/setter of description property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation::setDescription
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ExperienceTranslation::getDescription
     *
     * @return void
     */
    public function testGetAndSetSetDescription(): void {
        try {
            $this->object->setDescription("Description");
            $this->assertSame("Description", $this->object->getDescription());
        } catch (\Throwable $err) {
            $this->fail("No exception or error has to be thrown: " . $err->getMessage());
        }
    }

}
