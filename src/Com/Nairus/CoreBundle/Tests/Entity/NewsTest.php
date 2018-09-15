<?php

namespace Com\Nairus\CoreBundle\Entity;

use PHPUnit\Framework\TestCase;

/**
 * Test of News entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewsTest extends TestCase {

    /**
     * @var News
     */
    private $object;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new News();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * Test <code>removeContent</code> method.
     *
     * @covers Com\Nairus\CoreBundle\Entity\News::getContents
     * @covers Com\Nairus\CoreBundle\Entity\News::addContent
     * @covers Com\Nairus\CoreBundle\Entity\News::removeContent
     *
     * @return void
     */
    public function testRemoveContent(): void {
        $this->assertCount(0, $this->object->getContents(), "1. The contents collection musts to be empty.");
        $newsContent = new NewsContent();
        $newsContent
                ->setDescription("Description")
                ->setLocale("fr")
                ->setTitle("Title");
        $this->assertSame($this->object, $this->object->addContent($newsContent), "2. The [addContent] method has to return self instance.");
        $this->assertCount(1, $this->object->getContents(), "3. The contents collection has to contain one item.");
        $this->assertTrue($this->object->removeContent($newsContent), "4. The [removeContent] method has to return true.");
        $this->assertCount(0, $this->object->getContents(), "5. The contents collection has to be empty.");
    }

    /**
     * Test <code>__toString</code> method.
     *
     * @covers Com\Nairus\CoreBundle\Entity\News::__toString()
     *
     * @return void
     */
    public function testToString(): void {
        $this->assertEquals("[News] id:  (isNew=1)", $this->object->__toString(), "1. The [__toString] method has to be overrided.");
    }

}
