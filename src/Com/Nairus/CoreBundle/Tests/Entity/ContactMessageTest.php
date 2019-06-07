<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Validator\Constraints\Antiflood;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadContactMessage;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Test of ContactMessage entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactMessageTest extends AbstractKernelTestCase {

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        static::bootKernel();
        $this->loadDatas(static::$em, [new LoadContactMessage()]);
        $this->validator = static::$container->get("validator");
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->cleanDatas(static::$container, [ContactMessage::class, BlacklistedIp::class]);
        unset($this->validator);
    }

    /**
     * Test the flood validator.
     */
    public function testAntifloodValidator() {
        $contactMessage = new ContactMessage();
        $contactMessage->setEmail("goku@dbsuper.com")
                ->setIp("127.0.0.1")
                ->setMessage("Lorem ipsum dolor sit amet, consectetur cras amet.")
                ->setName("Son Goku")
                ->setRequestDate(new \DateTime());

        $errors = $this->validator->validate($contactMessage);
        $this->assertCount(1, $errors, "1. One error is expected");
        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];
        $this->assertInstanceOf(Antiflood::class, $error1->getConstraint(), "2. The constraint expected is not ok.");
    }

    /**
     * Test the name's validators.
     */
    public function testNameFieldValidators() {
        // prepare datas test.
        $contactMessage = new ContactMessage();
        $contactMessage->setEmail("goku@dbsuper.com")
                ->setIp("127.0.0.99")
                ->setMessage("Lorem ipsum dolor sit amet, consectetur cras amet.")
                ->setRequestDate(new \DateTime());

        $asserts = [
            Assert\NotBlank::class => "",
            Assert\Length::class => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam at nibh ut sem lobortis ullamcorper. Fusce bibendum lorem in turpis hendrerit, in vestibulum tortor hendrerit. Curabitur ac augue metus."
        ];
        $this->launchAssertions($asserts, $contactMessage, "setName");
    }

    /**
     * Test the phone's validators.
     */
    public function testPhoneFieldValidators() {
        // prepare datas test.
        $asserts = [
            Assert\Regex::class => "+33",
            Assert\Length::class => "07 01 01 01 01 01 01 07 01 01 01 01 01 01 07 01 01 01",
            Assert\Expression::class => null
        ];

        $contactMessage = new ContactMessage();
        $contactMessage->setIp("127.0.0.99")
                ->setMessage("Lorem ipsum dolor sit amet, consectetur cras amet.")
                ->setName("Son Goku")
                ->setRequestDate(new \DateTime());
        $this->launchAssertions($asserts, $contactMessage, "setPhone");
    }

    /**
     * Test the email's validators.
     */
    public function testEmailFieldValidators() {
        $asserts = [
            Assert\Length::class => "lorem-ipsum-dolor-sit-amet.consectetur-adipiscing-elit.nullam-at-nibh-ut-sem-lobortis-ullamcorper@lorem.com",
            Assert\Email::class => "nairus@"
        ];

        $contactMessage = new ContactMessage();
        $contactMessage->setIp("127.0.0.99")
                ->setMessage("Lorem ipsum dolor sit amet, consectetur cras amet.")
                ->setName("Son Goku")
                ->setRequestDate(new \DateTime());
        $this->launchAssertions($asserts, $contactMessage, "setEmail");
    }

    /**
     * Test the message's validators.
     */
    public function testMessageFieldValidators() {
        // prepare datas test.
        $contactMessage = new ContactMessage();
        $contactMessage->setEmail("goku@dbsuper.com")
                ->setIp("127.0.0.99")
                ->setName("Son Goku")
                ->setEmail("goku@son.jp")
                ->setRequestDate(new \DateTime());

        $asserts = [
            Assert\NotBlank::class => "",
            Assert\Length::class => "Lorem ipsum dolor sit amet."
        ];
        $this->launchAssertions($asserts, $contactMessage, "setMessage");
    }

    /**
     * Launch the assertions to test.
     *
     * @param array          $asserts        The assertions.
     * @param ContactMessage $contactMessage The entity to test.
     * @param string         $method         The method to set.
     *
     * @return void
     */
    private function launchAssertions(array $asserts, ContactMessage $contactMessage, string $method): void {
        $numTest = 1;
        foreach ($asserts as $assert => $value) {
            $contactMessage->$method($value);
            $errors = $this->validator->validate($contactMessage);
            $this->assertCount(1, $errors, "$numTest.1. One error is expected");
            /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
            $error1 = $errors[0];
            $this->assertInstanceOf($assert, $error1->getConstraint(), "$numTest.2. The constraint expected is not ok.");

            $numTest ++;
        }
    }

}
