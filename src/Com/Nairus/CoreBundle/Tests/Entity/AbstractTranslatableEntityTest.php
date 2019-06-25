<?php

namespace Com\Nairus\CoreBundle\Tests\Entity;

use Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity;
use PHPUnit\Framework\TestCase;

/**
 * Test of AbstractTranslatableEntity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AbstractTranslatableEntityTest extends TestCase {

    /**
     * @var BadTranslatableEntity
     */
    private $object;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new BadTranslatableEntity();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * Test the implementation of the entity.
     *
     * @return void
     */
    public function testImplementation(): void {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface", $this->object,
                "1. The entity musts implement [TranslatableEntityInterface] interface");
        $this->assertInstanceOf("Prezent\Doctrine\Translatable\Entity\AbstractTranslatable", $this->object,
                "2. The entity musts extends [AbstractTranslatable] Prezent mapped class");
    }

    /**
     * Test the implementation of isNew trait.
     *
     * @return void
     */
    public function isNew(): void {
        try {
            $this->assertFalse($this->object->isNew(), "1. The entity has be new.");
        } catch (\Exception $exc) {
            $this->fail("2. The entity has to implement isNew trait: " . $exc->getMessage());
        }
    }

    /**
     * Test the `getCurrentLocale` method.
     *
     * @covers Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity::getCurrentLocale
     * @covers Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity::setCurrentLocale
     *
     * @return void
     */
    public function testGetSetCurrentLocale(): void {
        $this->assertNull($this->object->getCurrentLocale(), "1. The current locale has to be null.");
        $this->assertSame($this->object, $this->object->setCurrentLocale("fr"), "2. The setter has to return self instance.");
        $this->assertSame("fr", $this->object->getCurrentLocale(), "3. The current locale has to be set correctly.");
    }

    /**
     * Test the `removeTranslation` method.
     *
     * @covers Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity::removeTranslation
     *
     * @return void
     */
    public function testRemoveTranslation(): void {
        $mockBadTranslationEntity = new Mock\BadTranslationEntity();
        $mockBadTranslationEntity->setLocale("fr");
        $this->object->addTranslation($mockBadTranslationEntity);
        $this->assertCount(1, $this->object->getTranslations(), "1. The translations collection has to contain one item.");
        $this->assertSame($this->object, $this->object->removeTranslation($mockBadTranslationEntity), "2. The remove method has to return self instance.");
        $this->assertCount(0, $this->object->getTranslations(), "3. The translations collection has to contain no item.");
    }

    /**
     * Test the `translate` method with `null` locale.
     *
     * @covers Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity::translate
     *
     * @expectedException \RuntimeException
     *
     * @return void
     */
    public function testTranslateWithNullLocale(): void {
        $this->object->translate();
    }

}
