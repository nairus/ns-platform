<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Avatar unit tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AvatarTest extends KernelTestCase {

    /**
     * @var Avatar
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        static::bootKernel();
        $this->object = new Avatar();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object);
    }

    public function testInstance() {
        $this->assertInstanceOf(\Com\Nairus\CoreBundle\Entity\ImageInterface::class, $this->object, "The entity has to implement [ImageInterface] interface");
    }

    /**
     * Validate the image uploaded.
     */
    public function testValidate() {
        $this->markTestIncomplete("TODO");
    }

}
