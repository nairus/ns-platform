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
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $DS = DIRECTORY_SEPARATOR;
        $baseImagePath = static::$kernel->getContainer()->getParameter('kernel.project_dir') . $DS . "tests" . $DS . "resources" . $DS;

        $badMimeType = new \Symfony\Component\HttpFoundation\File\UploadedFile($baseImagePath . "bad-image.bmp", "bad-image.bmp");
        $this->object->setImageFile($badMimeType);
        $badMimeTypeViolation = $validator->validate($this->object);
        $this->assertCount(1, $badMimeTypeViolation, "1.1 One error is expected.");
        /* @var $badMimeTypeError \Symfony\Component\Validator\ConstraintViolationListInterface */
        $badMimeTypeError = $badMimeTypeViolation[0];
        $this->assertInstanceOf(\Symfony\Component\Validator\Constraints\Image::class, $badMimeTypeError->getConstraint(), "1.2 The error has to an [Image] constraint.");

        $badRatioImage = new \Symfony\Component\HttpFoundation\File\UploadedFile($baseImagePath . "image-to-crop.jpg", "image-to-crop.jpg");
        $this->object->setImageFile($badRatioImage);
        $badRatioViolation = $validator->validate($this->object);
        $this->assertCount(1, $badRatioViolation, "2.1 One error is expected.");
        /* @var $badRationError \Symfony\Component\Validator\ConstraintViolationListInterface */
        $badRationError = $badRatioViolation[0];
        $this->assertInstanceOf(\Symfony\Component\Validator\Constraints\Image::class, $badRationError->getConstraint(), "2.2 The error has to an [Image] constraint.");

        $badSizeImage = new \Symfony\Component\HttpFoundation\File\UploadedFile($baseImagePath . "image-too-small.png", "image-too-small.png");
        $this->object->setImageFile($badSizeImage);
        $badSizeViolation = $validator->validate($this->object);
        $this->assertCount(1, $badSizeViolation, "3.1 One error is expected.");
        /* @var $errorBadSize \Symfony\Component\Validator\ConstraintViolationListInterface */
        $errorBadSize = $badSizeViolation[0];
        $this->assertInstanceOf(\Symfony\Component\Validator\Constraints\Image::class, $errorBadSize->getConstraint(), "3.2 The error has to an [Image] constraint.");
    }

}
