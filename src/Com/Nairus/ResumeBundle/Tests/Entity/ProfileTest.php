<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as SFConstaints;

/**
 * Profile entity unit tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ProfileTest extends AbstractKernelTestCase {

    /**
     * @var Profile
     */
    protected $object;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        static::bootKernel();
        $this->object = new Profile();
        $this->validator = static::$container->get("validator");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object, $this->validator);
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setPhone
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getPhone
     */
    public function testGetAndSetPhone() {
        try {
            $this->object->setPhone("04 01 01 01 01");
            $this->assertSame("04 01 01 01 01", $this->object->getPhone());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setCell
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getCell
     */
    public function testGetAndSetCell() {
        try {
            $this->object->setCell("06 01 01 01 01");
            $this->assertSame("06 01 01 01 01", $this->object->getCell());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setAddress
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getAddress
     */
    public function testGetAndSetAddress() {
        try {
            $this->object->setAddress("Adresse 1");
            $this->assertSame("Adresse 1", $this->object->getAddress());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setAddressAddition
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getAddressAddition
     */
    public function testGetAndSetAddressAddition() {
        try {
            $this->object->setAddressAddition("Adresse 2");
            $this->assertSame("Adresse 2", $this->object->getAddressAddition());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setCity
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getCity
     */
    public function testGetAndSetCity() {
        try {
            $this->object->setCity("Marseille");
            $this->assertSame("Marseille", $this->object->getCity());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setZip
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getZip
     */
    public function testGetAndSetZip() {
        try {
            $this->object->setZip("13004");
            $this->assertSame("13004", $this->object->getZip());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setCountry
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getCountry
     */
    public function testGetAndSetCountry() {
        try {
            $this->object->setCountry("France");
            $this->assertSame("France", $this->object->getCountry());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getFirstName()
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setFirstName()
     */
    public function testGetAndSetFirstName() {
        try {
            $this->object->setFirstName("Prénom");
            $this->assertSame("Prénom", $this->object->getFirstName());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getLastName()
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setLastName()
     */
    public function testGetAndSetLastName() {
        try {
            $this->object->setLastName("Nom");
            $this->assertSame("Nom", $this->object->getLastName());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::setUser
     * @covers Com\Nairus\ResumeBundle\Entity\Profile::getUser
     */
    public function testGetAndSetUser() {
        try {
            $user = new User();
            $this->object->setUser($user);
            $this->assertSame($user, $this->object->getUser());
        } catch (\Exception | \Error $exc) {
            $this->fail("Aucune exception/erreur ne doit être levé: " . $exc->getMessage());
        }
    }

    /**
     * Test firstName validation.
     *
     * @return void
     */
    public function testFirstNameValidation(): void {
        // Prepare entity valid datas.
        $this->object->setAddress("Address")
                ->setCity("Marseille")
                ->setPhone("06 02 03 04 05")
                ->setLastName("Surian")
                ->setZip("13004")
        ;

        // Case 1: Test NotBlank constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error1->getConstraint(), "1.2 NotBlank constraint is expected");
        $this->assertEquals("firstName", $error1->getPropertyPath(), "1.3 The firstName field is expected");

        // Case 2: Test Length constraint
        $this->object->setFirstName("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed lectus sed purus auctor orci aliquam.");
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "2.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "2.2 Length constraint is expected");
        $this->assertEquals("firstName", $error1->getPropertyPath(), "2.3 The firstName field is expected");
    }

    /**
     * Test lastName validation.
     *
     * @return void
     */
    public function testLastNameValidation(): void {
        // Prepare entity valid datas.
        $this->object->setAddress("Address")
                ->setCity("Marseille")
                ->setPhone("06 02 03 04 05")
                ->setFirstName("Nicolas")
                ->setZip("13004")
        ;

        // Case 1: Test NotBlank constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error1->getConstraint(), "1.2 NotBlank constraint is expected");
        $this->assertEquals("lastName", $error1->getPropertyPath(), "1.3 The firstName field is expected");

        // Case 2: Test Length constraint
        $this->object->setLastName("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed lectus sed purus auctor orci aliquam.");
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "2.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "2.2 NotBlank constraint is expected");
        $this->assertEquals("lastName", $error1->getPropertyPath(), "2.3 The lastName field is expected");
    }

    /**
     * Test phone validation.
     *
     * @return void
     */
    public function testPhoneValidation(): void {
        // Prepare entity valid datas.
        $this->object->setAddress("Address")
                ->setCity("Marseille")
                ->setFirstName("Nicolas")
                ->setLastName("Surian")
                ->setZip("13004")
        ;

        // Case 1: Test NotBlank constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Expression::class, $error1->getConstraint(), "1.2 The constraint expected is not ok");
        $this->assertEquals("phone", $error1->getPropertyPath(), "1.3 The phone field is expected");

        // Case 2: Test valid numbers
        $validNumbers = [
            1 => "06 01 02 03 04",
            2 => "0800 123 124",
            3 => "124 1224 111"
        ];
        foreach ($validNumbers as $idx => $number) {
            $this->object->setPhone($number);
            $errors = $this->validator->validate($this->object);
            $this->assertCount(0, $errors, "2.$idx No error is expected.");
        }

        // Case 3: Test Length constraint
        $this->object->setPhone("4701359900 453722009090 891219306 36727793 26970222282");
        $errors = $this->validator->validate($this->object);
        $error1 = $errors[0];
        $this->assertCount(1, $errors, "3.1 One error is expected");
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "3.2 The constraint expected is not ok");
        $this->assertEquals("phone", $error1->getPropertyPath(), "3.3 The phone field is expected");

        // Case 4: Test Regex constraint
        $invalidNumbers = [
            4 => "470.453.891.367.269.282",
            5 => " 798 987 791",
            6 => "789 1478 9874 ",
            7 => "789 aSf 797",
            8 => " 1645787 ",
        ];
        foreach ($invalidNumbers as $idx => $number) {
            $this->object->setPhone($number);
            $errors = $this->validator->validate($this->object);
            /* @var $error \Symfony\Component\Validator\ConstraintViolation */
            $error = $errors[0];
            $this->assertCount(1, $errors, "$idx.1 One error is expected");
            $this->assertInstanceOf(SFConstaints\Regex::class, $error->getConstraint(), "$idx.2 The constraint expected is not ok");
            $this->assertEquals("phone", $error->getPropertyPath(), "$idx.3 The phone field is expected");
        }
    }

    /**
     * Test cell validation.
     *
     * @return void
     */
    public function testCellValidation(): void {
        // Prepare entity valid datas.
        $this->object->setAddress("Address")
                ->setCity("Marseille")
                ->setFirstName("Nicolas")
                ->setPhone("06 01 02 03 04")
                ->setLastName("Surian")
                ->setZip("13004")
        ;

        // Case 1: Test valid numbers
        $validNumbers = [
            1 => "06 01 02 03 04",
            2 => "0800 123 124",
            3 => "124 1224 111"
        ];
        foreach ($validNumbers as $idx => $number) {
            $this->object->setCell($number);
            $errors = $this->validator->validate($this->object);
            $this->assertCount(0, $errors, "1.$idx The number has to be valid.");
        }

        // Case 2: Test Length constraint
        $this->object->setCell("4701359900 453722009090 891219306 36727793 26970222282");
        $errors = $this->validator->validate($this->object);
        $error1 = $errors[0];
        $this->assertCount(1, $errors, "2.1 One error is expected");
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "2.2 The constraint expected is not ok");
        $this->assertEquals("cell", $error1->getPropertyPath(), "2.3 The cell field is expected");

        // Case 3: Test Regex constraint
        $invalidNumbers = [
            3 => "470.453.891.367.269.282",
            4 => " 798 987 791",
            5 => "789 1478 9874 ",
            6 => "789 aSf 797",
            7 => " 1645787 ",
        ];
        foreach ($invalidNumbers as $idx => $number) {
            $this->object->setCell($number);
            $errors = $this->validator->validate($this->object);
            /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
            $error = $errors[0];
            $this->assertCount(1, $errors, "$idx.1 One error is expected");
            $this->assertInstanceOf(SFConstaints\Regex::class, $error->getConstraint(), "$idx.2 The constraint expected is not ok");
            $this->assertEquals("cell", $error->getPropertyPath(), "$idx.3 The cell field is expected");
        }
    }

    /**
     * Test address validation.
     *
     * @return void
     */
    public function testAddressValidation(): void {
        // Prepare entity valid datas.
        $this->object->setLastName("Surian")
                ->setCity("Marseille")
                ->setPhone("06 02 03 04 05")
                ->setFirstName("Nicolas")
                ->setZip("13004")
        ;

        // Case 1: Test NotBlank constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error1->getConstraint(), "1.2 NotBlank constraint is expected");
        $this->assertEquals("address", $error1->getPropertyPath(), "1.3 The address field is expected");
    }

    /**
     * Test city validation.
     *
     * @return void
     */
    public function testCityValidation(): void {
        // Prepare entity valid datas.
        $this->object->setLastName("Surian")
                ->setAddress("Address")
                ->setPhone("06 02 03 04 05")
                ->setFirstName("Nicolas")
                ->setZip("13004")
        ;

        // Case 1: Test NotBlank constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error1->getConstraint(), "1.2 NotBlank constraint is expected");
        $this->assertEquals("city", $error1->getPropertyPath(), "1.3 The city field is expected");

        // Case 2: Test Length constraint
        $this->object->setCity("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed lectus sed purus auctor orci aliquam.");
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "2.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "2.2 Length constraint is expected");
        $this->assertEquals("city", $error1->getPropertyPath(), "2.3 The city field is expected");
    }

    /**
     * Test zip validation.
     *
     * @return void
     */
    public function testZipValidation(): void {
        // Prepare entity valid datas.
        $this->object->setLastName("Surian")
                ->setAddress("Address")
                ->setPhone("06 02 03 04 05")
                ->setFirstName("Nicolas")
                ->setLastName("Surian")
                ->setCity("Marseille")
        ;

        // Case 1: Test NotBlank constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\NotBlank::class, $error1->getConstraint(), "1.2 NotBlank constraint is expected");
        $this->assertEquals("zip", $error1->getPropertyPath(), "1.3 The zip field is expected");

        // Case 2: Test valid zip code
        $validZipCodes = [
            1 => "13004", // Marseille France
            2 => "SW1A 2", // London UK,
            3 => "E1", // Aldgate East UK
            4 => "10801 10802 10804 10805", // New York USA
        ];

        foreach ($validZipCodes as $idx => $zip) {
            $this->object->setZip($zip);
            $errors = $this->validator->validate($this->object);
            $this->assertCount(0, $errors, "2.$idx No error is expected");
        }

        // Case 3: Test Length constraint
        $this->object->setZip("4701359900 453722009090 891219306 36727793 26970222282");
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "3.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "3.2 Length constraint is expected");
        $this->assertEquals("zip", $error1->getPropertyPath(), "3.3 The zip field is expected");

        // Case 4: Test Regex constraint
        $invalidZipCodes = [
            4 => " AZZ EEEA ",
            5 => "aze1 1452",
            6 => "12347-478996",
        ];

        foreach ($invalidZipCodes as $idx => $zip) {
            $this->object->setZip($zip);
            $errors = $this->validator->validate($this->object);
            $this->assertCount(1, $errors, "$idx.1 One error is expected");
            /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
            $error1 = $errors[0];
            $this->assertInstanceOf(SFConstaints\Regex::class, $error1->getConstraint(), "$idx.2 Regex constraint is expected");
            $this->assertEquals("zip", $error1->getPropertyPath(), "$idx.3 The zip field is expected");
        }
    }

    /**
     * Test country validation.
     *
     * @return void
     */
    public function testCountryValidation(): void {
        // Prepare entity valid datas.
        $this->object->setLastName("Surian")
                ->setAddress("Address")
                ->setPhone("06 02 03 04 05")
                ->setFirstName("Nicolas")
                ->setZip("13004")
                ->setCity("Marseille")
                ->setCountry("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed lectus sed purus auctor orci aliquam.")
        ;

        // Case 1: Test Length constraint
        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Length::class, $error1->getConstraint(), "1.2 Length constraint is expected");
        $this->assertEquals("country", $error1->getPropertyPath(), "1.3 The country field is expected");
    }

    /**
     * Test avatar validation.
     *
     * @return void
     */
    public function testAvatarValidation(): void {
        // Prepare entity valid datas.
        $this->object->setLastName("Surian")
                ->setAddress("Address")
                ->setPhone("06 02 03 04 05")
                ->setFirstName("Nicolas")
                ->setZip("13004")
                ->setCity("Marseille")
        ;

        // Case 1: Test IsValid constraint
        $DS = DIRECTORY_SEPARATOR;
        $baseImagePath = static::$kernel->getContainer()->getParameter('kernel.project_dir') . $DS . "tests" . $DS . "resources" . $DS;

        $badMimeType = new \Symfony\Component\HttpFoundation\File\UploadedFile($baseImagePath . "bad-image.bmp", "bad-image.bmp");
        $avatar = new Avatar();
        $avatar->setImageFile($badMimeType);
        $this->object->setAvatar($avatar);

        $errors = $this->validator->validate($this->object);
        $this->assertCount(1, $errors, "1.1 One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(SFConstaints\Image::class, $error1->getConstraint(), "1.2 IsValid constraint is expected");
        $this->assertEquals("avatar.imageFile", $error1->getPropertyPath(), "1.3 The avatar.imageFile field is expected");
    }

}
