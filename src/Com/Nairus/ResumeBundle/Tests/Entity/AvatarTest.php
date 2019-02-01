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
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::setImageSrcPath
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::getImageSrcPath
     */
    public function testGetAndSetImageSrcPath() {
        try {
            $imagePath = "/path/to/image.png";
            $this->object->setImageSrcPath($imagePath);
            $this->assertSame($imagePath, $this->object->getImageSrcPath());

            // Tests PHP 7 :
            $this->object->setImageSrcPath(true); // typecasting en (string)"1"
            $this->object->setImageSrcPath(false); // typecasting en (string)""
            $this->object->setImageSrcPath(42); // typecasting en (string)"42"
            $this->object->setImageSrcPath(1.5); // typecasting en (string)"1.5"
            $this->object->setImageSrcPath(0); // typecasting en (string)"0"
        } catch (\Exception $exc) {
            $this->fail("Aucune exception ne doit être levé: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("Aucune erreur ne doit être levée :" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::setImageSrcPath
     *
     * @expectedException \TypeError
     */
    public function testSetImageSrcPathWithNullParam() {
        $this->object->setImageSrcPath(null);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::setImageSrcPath
     *
     * @expectedException \TypeError
     */
    public function testSetImageSrcPathWithBadParam() {
        $this->object->setImageSrcPath(array());
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::setImageThbPath
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::getImageThbPath
     */
    public function testGetAndSetImageThbPath() {
        try {
            $imagePath = "/path/to/image.png";
            $this->object->setImageThbPath($imagePath);
            $this->assertSame($imagePath, $this->object->getImageThbPath());
        } catch (\Exception $exc) {
            $this->fail("Aucune exception ne doit être levé: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("Aucune erreur ne doit être levée :" . $err->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Avatar::setImageThbPath
     *
     * @expectedException \TypeError
     */
    public function testSetImageThbPathWithBadParam() {
        $this->object->setImageThbPath(array());
    }

}
