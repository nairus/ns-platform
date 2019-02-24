<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Entity\Avatar;
use Com\Nairus\CoreBundle\Exception\ImageProcessingException;
use Com\Nairus\ResumeBundle\Tests\Repository\AbstractAvatarRepositoryTestCase;

/**
 * Test insert Avatar with no image.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AvatarRepositoryInsertWithNoImageTest extends AbstractAvatarRepositoryTestCase {

    /**
     * The error case.
     *
     * @return void
     */
    public function testErrorCase(): void {
        try {
            static::$em->persist(new Avatar());
            static::$em->flush();
            static::$em->clear();
            $this->fail("An exception is expected.");
        } catch (\Throwable $exc) {
            $this->assertInstanceOf(ImageProcessingException::class, $exc, "1. A [ImageProcessingException] exception is expected.");
            $avatars = static::$repository->findAll();
            $this->assertCount(0, $avatars, "2. No entity is expected in database.");
            $this->assertEquals(ImageProcessingException::NO_IMAGE_TO_PROCESS_ERROR, $exc->getCode(), "3. The code expected is not ok");
        }
    }

}
