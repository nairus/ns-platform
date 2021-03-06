<?php

namespace Com\Nairus\ResumeBundle\Collection;

use Com\Nairus\ResumeBundle\Entity\Resume;
use PHPUnit\Framework\TestCase;

/**
 * Test of resume entities collection.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeCollectionTest extends TestCase {

    /**
     * @var ResumeCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ResumeCollection();
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::offsetSet
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::offsetGet
     */
    public function testOffsetGetAndSet() {
        $resume1 = new Resume();
        $resume1->setCurrentLocale("fr")
                ->setTitle("Titre 1");

        $resume2 = new Resume();
        $resume2->setCurrentLocale("fr")
                ->setTitle("Titre 2");

        $this->object[] = $resume1;
        $this->object[] = $resume2;

        $this->assertCount(2, $this->object, "1. Il doit y avoir 2 Entités dans la collection.");
        for ($index = 0; $index < $this->object->count(); $index++) {
            $this->assertInstanceOf(Resume::class, $this->object[$index], "2." . ($index + 1) . ".1 L'entité doit être de type [Resume].");
            $this->assertSame("Titre " . ($index + 1), $this->object[$index]->getTitle(), "2." . ($index + 1) . " La propriété [title] doit être correcte.");
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::offsetSet
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage [Resume] entity expected.
     */
    public function testOffsetSetWithBadEntity() {
        $this->object[] = new \stdClass();
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::add
     */
    public function testAdd() {
        try {
            $resume = new Resume();
            $this->assertCount(0, $this->object, "1. 0 Entité doit être dans la collection.");
            $this->assertTrue($this->object->add($resume), "2 Aucune exception ne doit être levée.");
            $this->assertCount(1, $this->object, "3. Une entité doit être dans la collection.");
            $resume2 = new Resume();
            $this->assertTrue($this->object->add($resume2), "4 Aucune exception ne doit être levée.");
            $this->assertCount(2, $this->object, "5. Deux entités doivent être dans la collection.");
        } catch (\Exception | \Error $ex) {
            $this->fail("Aucune exception / erreur ne doit être levée: " . $ex->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::add
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage [Resume] entity expected.
     */
    public function testAddWithBadEntity() {
        $this->object->add(new \stdClass());
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::set
     */
    public function testSet() {
        try {
            $resume = new Resume();
            $this->assertCount(0, $this->object, "1. Aucune entité ne doit être dans la collection.");
            $this->object->set(0, $resume);
            $this->assertTrue(true, "2. Aucune exception ne doit être levée.");
            $this->assertCount(1, $this->object, "3. Une entité doit être dans la collection.");
            $resume2 = new Resume();
            $this->object->set(0, $resume2);
            $this->assertCount(1, $this->object, "4. Une seule entité doit toujours être dans la collection.");
            $this->assertNotSame($resume, $this->object[0], "5. L'entité dans la collection doit être différente.");
        } catch (\Exception | \Error $ex) {
            $this->fail("Aucune exception / erreur ne doit être levée: " . $ex->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::set
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage [Resume] entity expected.
     */
    public function testSetWithBadEntity() {
        $this->object->set(0, new \stdClass());
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::current
     */
    public function testCurrent() {
        $resume1 = new Resume();
        $resume2 = new Resume();
        $this->assertFalse($this->object->current(), "1. Il ne doit y avoir aucune entité.");
        $this->object->add($resume1);
        $this->object->add($resume2);
        $this->assertNotNull($this->object->current(), "2. L'entité courante ne doit pas être null.");
        $this->object->next();
        $this->assertNotNull($this->object->current(), "3. L'entité courante ne doit pas être null.");
        $this->assertNotSame($resume1, $this->object->current(), "4. L'entité courante ne doit pas être différente.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::first
     */
    public function testFirst() {
        $this->assertFalse($this->object->first(), "1. Il ne doit y avoir aucune entité.");
        $resume1 = new Resume();
        $resume2 = new Resume();
        $this->object->add($resume1);
        $this->object->add($resume2);
        $this->assertNotNull($this->object->first(), "2. La première entité doit être présente.");
        $this->assertNotSame($resume2, $this->object->first(), "3. La première entité doit être correcte.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::get
     */
    public function testGet() {
        $resume1 = new Resume();
        $resume2 = new Resume();
        $this->object->add($resume1);
        $this->object->add($resume2);
        $this->assertNotNull($this->object->get(0), "1. L'entité doit être présente.");
        $this->assertNotNull($this->object->get(1), "2. L'entité doit être présente.");
        $this->assertNotSame($this->object->get(0), $this->object->get(1), "3. Les 2 entités de la collection doivent être différentes.");
        $this->assertNull($this->object->get(2), "4. Aucune entité ne doit être en 3ème position.");
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Collection\ResumeCollection::last
     */
    public function testLast() {
        $this->assertFalse($this->object->last(), "1. Il ne doit y avoir aucune entité.");
        $resume1 = new Resume();
        $resume2 = new Resume();
        $this->object->add($resume1);
        $this->object->add($resume2);
        $this->assertNotNull($this->object->last(), "2. La dernière entité doit être présente.");
        $this->assertNotSame($resume1, $this->object->last(), "3. La dernière entité doit être correcte.");
    }

    /**
     * Test the constructor with bad elements.
     *
     * @expectedException \TypeError
     *
     * @return void
     */
    public function testConstructorWithBadElements(): void {
        new ResumeCollection([new \Com\Nairus\ResumeBundle\Entity\ResumeSkill()]);
    }

}
