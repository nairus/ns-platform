<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Test of NewContent.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewContentTest extends AbstractKernelTestCase {

    /**
     * Test the validation of entity's fields.
     *
     * Test NotBlank constraint.
     *
     * @return void
     */
    public function testValidateEntityCase1(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        $newsContent = new NewsContent();
        $newsContent->setDescription("")
                ->setLink(false)
                ->setTitle(null);
        $errors = $validator->validate($newsContent);
        $this->assertCount(3, $errors, "1. 3 errors are expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        /* @var $error2 \Symfony\Component\Validator\ConstraintViolation */
        $error2 = $errors[1];
        /* @var $error3 \Symfony\Component\Validator\ConstraintViolation */
        $error3 = $errors[2];

        $this->assertInstanceOf(NotBlank::class, $error1->getConstraint(), "2. The first error has to be a NotNull constraint.");
        $this->assertInstanceOf(NotBlank::class, $error2->getConstraint(), "3. The second error has to be a NotNull constraint.");
        $this->assertInstanceOf(NotBlank::class, $error3->getConstraint(), "4. The third error has to be a NotNull constraint.");
    }

    /**
     * Test the validation of entity's fields.
     *
     * Test Bad Url
     *
     * @return void
     */
    public function testValidateEntityCase2(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        $newsContent = new NewsContent();
        $newsContent->setDescription("Good description")
                ->setTitle("Good title")
                ->setLink("bad-url");
        $errors = $validator->validate($newsContent);
        $this->assertCount(1, $errors, "1. 1 error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];

        $this->assertInstanceOf(Url::class, $error1->getConstraint(), "2. The error has to be a Url constraint.");
        $this->assertContains("Cette valeur n'est pas une URL valide", $error1->getMessage(), "3. The message excepted is not ok.");
    }

    /**
     * Test the validation of entity's fields.
     *
     * @return void
     */
    public function testValidateEntityCase3(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        // Test bad url protocol
        $newsContent = new NewsContent();
        $newsContent->setDescription("Good description")
                ->setTitle("Good title")
                ->setLink("ftp://www.bad-protocol.com");
        $errors = $validator->validate($newsContent);
        $this->assertCount(1, $errors, "1. 1 error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];

        $this->assertInstanceOf(Url::class, $error1->getConstraint(), "2. The error has to be a Url constraint.");
    }

    /**
     * Test the validation of entity's fields.
     *
     * Test Length constraint
     *
     * @return void
     */
    public function testValidateEntityCase4(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        // Test Length constraint
        $newsContent = new NewsContent();
        $newsContent->setDescription("Good description")
                ->setTitle("Lorem ipsum dolor sit amet consectetur cras amet too long")
                ->setLink("http://www.goodurl.com");
        $errors = $validator->validate($newsContent);
        $this->assertCount(1, $errors, "1. 1 error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];

        $this->assertInstanceOf(Length::class, $error1->getConstraint(), "2. The error has to be a Length constraint.");
        $this->assertContains("maximum 50 caractères", $error1->getMessage(), "3. The message excepted is not ok.");
    }

    /**
     * Test the validation of entity's fields.
     *
     * Good validation case.
     *
     * @return void
     */
    public function testValidateEntityCase5(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$container->get("validator");

        // Test Length constraint
        $newsContent = new NewsContent();
        $newsContent->setDescription("Good description")
                ->setTitle("Good title")
                ->setLink("https://www.goodurl.com");
        $errors = $validator->validate($newsContent);
        $this->assertCount(0, $errors, "1. no error is expected.");
    }

}
