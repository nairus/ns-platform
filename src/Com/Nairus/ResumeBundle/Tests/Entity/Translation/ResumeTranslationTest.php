<?php

namespace Com\Nairus\ResumeBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Test of ResumeTranslation
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeTranslationTest extends KernelTestCase {

    /**
     * @var ResumeTranslation
     */
    private $object;

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        static::bootKernel();
        $this->object = new ResumeTranslation();
    }

    /**
     * Test the implementation of the entity.
     *
     * @return void
     */
    public function testImplementation(): void {
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity", $this->object, "1. The entity musts be an instance of [AbstractTranslationEntity].");
    }

    /**
     * Test bad object instance.
     *
     * @expectedException \TypeError
     * @expectedExceptionMessage Instance of [Resume] expected!
     *
     * @return void
     */
    public function testBadObjectInstance(): void {
        $this->object->setTranslatable(new \Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslatableEntity());
    }

    /**
     * Test the getter/setter of title property.
     *
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation::setTitle
     * @covers Com\Nairus\ResumeBundle\Entity\Translation\ResumeTranslation::getTitle
     *
     * @return void
     */
    public function testGetAndSetTitle(): void {
        try {
            $this->object->setTitle('Titre');
            $this->assertSame("Titre", $this->object->getTitle());
        } catch (\Exception $exc) {
            $this->fail("No exception has to be thrown: " . $exc->getMessage());
        } catch (\Error $err) {
            $this->fail("No error has to be thrown:" . $err->getMessage());
        }
    }

    /**
     * Test the NotBlank constraint of the entity.
     *
     * @return void
     */
    public function testValidationNotBlank(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $resumeTranslation = new ResumeTranslation();
        $errors = $validator->validate($resumeTranslation);
        $this->assertCount(1, $errors, "1. One error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];

        $this->assertInstanceOf(NotBlank::class, $error1->getConstraint(), "2. The error has to be a NotNull constraint.");
    }

    /**
     * Test the Length constraint of the entity.
     *
     * @return void
     */
    public function testValidationLength(): void {
        /* @var $validator \Symfony\Component\Validator\Validator\ValidatorInterface */
        $validator = static::$kernel->getContainer()->get("validator");

        $resumeTranslation = new ResumeTranslation();
        $resumeTranslation->setTitle("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam luctus tincidunt elit vel cras amet bad.");
        $errors = $validator->validate($resumeTranslation);
        $this->assertCount(1, $errors, "1. One error is expected.");

        /* @var $error1 \Symfony\Component\Validator\ConstraintViolation */
        $error1 = $errors[0];

        $this->assertInstanceOf(Length::class, $error1->getConstraint(), "2. The error has to be a Length constraint.");
    }

    /**
     * Test the <code>setSlug</code> proxy method.
     *
     * @return void
     */
    public function testSetSlugWithBadValue(): void {
        $badValues = [
            "1" => null,
            "2" => 1,
            "3" => new stdClass(),
            "4" => []
        ];

        foreach ($badValues as $key => $value) {
            try {
                $this->object->setSlug($value);
                $this->fail("A [TypeError] error is expected.");
            } catch (\Throwable $exc) {
                $this->assertInstanceOf(\TypeError::class, $exc,
                        $key . ". The exception expected is not ok: " . $exc->getMessage()
                );
            }
        }
    }

}
