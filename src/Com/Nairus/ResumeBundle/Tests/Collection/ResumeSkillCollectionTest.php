<?php

namespace Com\Nairus\ResumeBundle\Collection;

use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use PHPUnit\Framework\TestCase;

/**
 * Test of resumeSkill entities collection.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeSkillCollectionTest extends TestCase {

    /**
     * @var ResumeSkillCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ResumeSkillCollection;
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::add
     */
    public function testAdd() {
        try {
            $entity = new ResumeSkill();
            $this->assertCount(0, $this->object, "1. 0 Entité doit être dans la collection.");
            $this->assertTrue($this->object->add($entity), "2 Aucune exception ne doit être levée.");
            $this->assertCount(1, $this->object, "3. Une entité doit être dans la collection.");
            $entity2 = new ResumeSkill();
            $this->assertTrue($this->object->add($entity2), "4 Aucune exception ne doit être levée.");
            $this->assertCount(2, $this->object, "5. Deux entités doivent être dans la collection.");
        } catch (\Exception | \Error $ex) {
            $this->fail("Aucune exception / erreur ne doit être levée: " . $ex->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::add
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage [ResumeSkill] entity expected.
     */
    public function testAddWithBadEntity() {
        $this->object->add(new \stdClass());
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::current
     */
    public function testCurrent() {
        $entity1 = new ResumeSkill();
        $entity2 = new ResumeSkill();
        $this->assertFalse($this->object->current(), "1. Il ne doit y avoir aucune entité.");
        $this->object->add($entity1);
        $this->object->add($entity2);
        $this->assertNotNull($this->object->current(), "2. L'entité courante ne doit pas être null.");
        $this->object->next();
        $this->assertNotNull($this->object->current(), "3. L'entité courante ne doit pas être null.");
        $this->assertNotSame($entity1, $this->object->current(), "4. L'entité courante ne doit pas être différente.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::first
     */
    public function testFirst() {
        $this->assertFalse($this->object->first(), "1. Il ne doit y avoir aucune entité.");
        $entity1 = new ResumeSkill();
        $entity2 = new ResumeSkill();
        $this->object->add($entity1);
        $this->object->add($entity2);
        $this->assertNotNull($this->object->first(), "2. La première entité doit être présente.");
        $this->assertNotSame($entity2, $this->object->first(), "3. La première entité doit être correcte.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::get
     */
    public function testGet() {
        $entity1 = new ResumeSkill();
        $entity2 = new ResumeSkill();
        $this->object->add($entity1);
        $this->object->add($entity2);
        $this->assertNotNull($this->object->get(0), "1. L'entité doit être présente.");
        $this->assertNotNull($this->object->get(1), "2. L'entité doit être présente.");
        $this->assertNotSame($this->object->get(0), $this->object->get(1), "3. Les 2 entités de la collection doivent être différentes.");
        $this->assertNull($this->object->get(2), "4. Aucune entité ne doit être en 3ème position.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::last
     */
    public function testLast() {
        $this->assertFalse($this->object->last(), "1. Il ne doit y avoir aucune entité.");
        $entity1 = new ResumeSkill();
        $entity2 = new ResumeSkill();
        $this->object->add($entity1);
        $this->object->add($entity2);
        $this->assertNotNull($this->object->last(), "2. La dernière entité doit être présente.");
        $this->assertNotSame($entity1, $this->object->last(), "3. La dernière entité doit être correcte.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::offsetGet
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::offsetSet
     */
    public function testOffsetGetAndSet() {
        $entity1 = new ResumeSkill();
        $entity1->setRank("1");

        $entity2 = new ResumeSkill();
        $entity2->setRank("2");

        $this->object[] = $entity1;
        $this->object[] = $entity2;

        $this->assertCount(2, $this->object, "1. Il doit y avoir 2 Entités dans la collection.");
        for ($index = 0; $index < $this->object->count(); $index++) {
            $this->assertInstanceOf(ResumeSkill::class, $this->object[$index], "2." . ($index + 1) . ".1 L'entité doit être de type [ResumeSkill].");
            $this->assertSame(($index + 1), $this->object[$index]->getRank(), "2." . ($index + 1) . " La propriété [description] doit être correcte.");
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::offsetSet
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage [ResumeSkill] entity expected.
     */
    public function testOffsetSetWithBadEntity() {
        $this->object[] = new \stdClass();
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::set
     */
    public function testSet() {
        try {
            $entity1 = new ResumeSkill();
            $this->assertCount(0, $this->object, "1. Aucune entité ne doit être dans la collection.");
            $this->object->set(0, $entity1);
            $this->assertTrue(true, "2. Aucune exception ne doit être levée.");
            $this->assertCount(1, $this->object, "3. Une entité doit être dans la collection.");
            $entity2 = new ResumeSkill();
            $this->object->set(0, $entity2);
            $this->assertCount(1, $this->object, "4. Une seule entité doit toujours être dans la collection.");
            $this->assertNotSame($entity1, $this->object[0], "5. L'entité dans la collection doit être différente.");
        } catch (\Exception | \Error $ex) {
            $this->fail("Aucune exception / erreur ne doit être levée: " . $ex->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection::set
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage [ResumeSkill] entity expected.
     */
    public function testSetWithBadEntity() {
        $this->object->set(0, new \stdClass());
    }

    /**
     * Test the constructor with bad elements.
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testConstructorWithBadElements(): void {
        new ResumeSkillCollection([new \Com\Nairus\ResumeBundle\Entity\Resume()]);
    }

}
