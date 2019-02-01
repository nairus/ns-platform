<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Avatar;

/**
 * Test Avatar Repository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class AvatarRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var AvatarRepository
     */
    private static $repository;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Avatar");
    }

    /**
     * Test entity insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        $newAvatar = new Avatar();
        $newAvatar
                ->setImageSrcPath("image/src/path.png")
                ->setImageThbPath("image/thb/path.png");

        static::$em->persist($newAvatar);
        static::$em->flush();
        static::$em->clear();

        $avatars = static::$repository->findAll();
        $this->assertCount(1, $avatars, "1.1. Il doit y avoir 1 entité en base.");
        /* @var $avatar Avatar */
        $avatar = $avatars[0];
        $this->assertSame("image/src/path.png", $avatar->getImageSrcPath(), "1.2. Le champ [imageSrcPath] doit être identique.");
        $this->assertSame("image/thb/path.png", $avatar->getImageThbPath(), "1.3. Le champ [imageThbPath] doit être identique.");

        // Test update.
        $avatar
                ->setImageSrcPath("image/src/path2.png")
                ->setImageThbPath("image/thb/path2.png");
        static::$em->flush();
        static::$em->clear();
        $avatarUpdated = static::$repository->find($avatar->getId());
        $this->assertSame("image/thb/path2.png", $avatarUpdated->getImageThbPath(), "2.1. Le champ [imageThbPath] doit être identique.");
        $this->assertSame("image/src/path2.png", $avatarUpdated->getImageSrcPath(), "2.2. Le champ [imageSrcPath] doit être identique.");

        // Test delete.
        $id = $avatarUpdated->getId();
        static::$em->remove($avatarUpdated);
        static::$em->flush();
        static::$em->clear();
        $avatarDeleted = static::$repository->find($id);
        $this->assertNull($avatarDeleted, "3.1. L'entité doit être supprimée.");
    }

}
