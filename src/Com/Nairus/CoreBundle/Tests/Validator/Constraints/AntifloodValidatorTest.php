<?php

namespace Com\Nairus\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;

/**
 * Test of AntifloodValidator.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AntifloodValidatorTest extends TestCase {

    /**
     * Test the error constants of the validator.
     *
     * @return void
     */
    public function testConstants(): void {
        $this->assertEquals(1, AntifloodValidator::BAD_REPOSITORY_ERROR, "1. The code error expected is not ok.");
        $this->assertEquals(2, AntifloodValidator::BAD_ENTITY_ERROR, "2. The code error expected is not ok.");
    }

    /**
     * Test the flood validator.
     *
     * @expectedException \Symfony\Component\Validator\Exception\ValidatorException
     * @expectedExceptionCode 2
     */
    public function testAntifloodValidatorWithBadEntity(): void {
        // prepare the mocks
        $entityManagerMock = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
                ->method("getRepository")
                ->willReturn($this->createMock(\Com\Nairus\CoreBundle\Validator\Antifloodable::class));

        $validator = new AntifloodValidator($entityManagerMock);

        // launch the test
        $constraint = new Antiflood([Antiflood::OPTION_SECONDS => 1]);
        $errors = $validator->validate(new \stdClass(), $constraint);
        $this->assertNull($errors, "1. No error is expected");
    }

    /**
     * Test the flood validator.
     *
     * @expectedException \Symfony\Component\Validator\Exception\ValidatorException
     * @expectedExceptionCode 1
     */
    public function testAntifloodValidatorWithBadRespository(): void {
        // prepare the mocks
        $entityManagerMock = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
                ->method("getRepository")
                ->willReturn($this->createMock(\Doctrine\Common\Persistence\ObjectRepository::class));

        $antifloodEntity = $this->createMock(\Com\Nairus\CoreBundle\Entity\IpTraceable::class);
        $antifloodEntity->expects($this->exactly(0))
                ->method("getIp");

        // launch the test
        $validator = new AntifloodValidator($entityManagerMock);
        $constraint = new Antiflood([Antiflood::OPTION_SECONDS => 1]);
        $validator->validate($antifloodEntity, $constraint);
    }

}
