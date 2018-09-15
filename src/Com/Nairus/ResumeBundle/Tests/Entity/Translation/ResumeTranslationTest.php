<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test of ResumeTranslation
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeTranslationTest extends KernelTestCase {

    /**
     * @var ResumeTranslation
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
        $this->object = new ResumeTranslation();
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
     * @expectedExceptionMessage Instance of [Resume] expected!
     *
     * @return void
     */
    public function testBadObjectInstance(): void {
        $this->object->setTranslatable(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity());
    }

    /**
     * Test the getter/setter of title property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation::setTitle
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation::getTitle
     *
     * @return void
     */
    public function testGetAndSetTitle(): void {
        try {
            $this->object->setTitle('Titre');
            $this->assertSame("Titre", $this->object->getTitle());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation::setTitle
     *
     * @return void
     *
     * @expectedException \TypeError
     */
    public function testSetTitleWithNullParam(): void {
        $this->object->setTitle(null);
    }

}
