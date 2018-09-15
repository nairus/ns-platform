<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test of EducationTranslation.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EducationTranslationTest extends KernelTestCase {

    /**
     * @var EducationTranslation
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
        $this->object = new EducationTranslation();
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
     * @expectedExceptionMessage Instance of [Education] expected!
     *
     * @return void
     */
    public function testBadObjectInstance(): void {
        $this->object->setTranslatable(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity());
    }

    /**
     * Test the getter/setter of description property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation::setDescription
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation::getDescription
     *
     * @return void
     */
    public function testGetAndSetDescription(): void {
        try {
            $desc = "Lorem ipsum ...";
            $this->object->setDescription($desc);
            $this->assertSame($desc, $this->object->getDescription());
        } catch (\Throwable $exc) {
            $this->fail("No exception or error has to be thrown: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\EducationTranslation::setDescription
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testSetDescriptionWithNullParam() {
        $this->object->setDescription(null);
    }

}
