<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\ResumeBundle\Tests\Repository\AbstractAvatarRepositoryTestCase;
use Com\Nairus\ResumeBundle\Entity\Avatar;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Test Avatar Repository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class AvatarRepositoryTest extends AbstractAvatarRepositoryTestCase {

    /**
     * Test entity insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        $DS = DIRECTORY_SEPARATOR;
        $imagePath = static::$projectDirectory . $DS . "tests" . $DS . "resources" . $DS . "image-to-resize.png";
        $newAvatar = new Avatar();
        $newAvatar
                ->setImageFile(new UploadedFile($imagePath, "image-to-resize.png"));

        static::$em->persist($newAvatar);
        static::$em->flush();
        static::$em->clear();

        $avatars = static::$repository->findAll();
        $this->assertCount(1, $avatars, "1.1. One entity is expected in database.");
        /* @var $avatar Avatar */
        $avatar = $avatars[0];

        $this->assertSame("png", $avatar->getExtension(), "1.2. The extension field has to be well filled.");
        // see https://regex101.com/r/Cf6dWm/1
        $this->assertRegExp("~^\\" . $DS . "tests\\" . $DS . "image_manager\\" . $DS . "avatar\\" . $DS . "[0-9]{1}\\" . $DS . "[0-9]{1}\\" . $DS . "$~",
                $avatar->getRelativePath(), "1.3. The [relativeField] field has to be well built.");
        $this->assertTrue($this->findImage("src-" . $avatar->getId() . ".png"), "1.4 The source image has to be created");
        $this->assertTrue($this->findImage("thb-" . $avatar->getId() . ".png"), "1.5 The thumbnail image has to be created");

        // Test update.
        $expectedRelativePath = $avatar->getRelativePath();
        $imagePath = static::$projectDirectory . $DS . "tests" . $DS . "resources" . $DS . "image-to-resize.jpg";
        $uploadedFile = new UploadedFile($imagePath, "image-to-resize.jpg");
        // NOTE: we need to update this fields manually to dispatch update event.
        $avatar
                ->setImageFile($uploadedFile)
                ->setTmpExtension($avatar->getExtension())
                ->setExtension($uploadedFile->getExtension());
        static::$em->flush();
        static::$em->clear();
        /* @var $avatarUpdated Avatar */
        $avatarUpdated = static::$repository->find($avatar->getId());
        $this->assertSame("jpg", $avatarUpdated->getExtension(), "2.1. The [extension] field has to be changed.");
        $this->assertSame($expectedRelativePath, $avatarUpdated->getRelativePath(), "2.2. The [relativeField] field has to remain identical.");
        $this->assertTrue($this->findImage("src-" . $avatar->getId() . ".jpg"), "2.4 The new source image has to be created");
        $this->assertTrue($this->findImage("thb-" . $avatar->getId() . ".jpg"), "2.5 The new thumbnail image has to be created");
        $this->assertFalse($this->findImage("src-" . $avatar->getId() . ".png"), "2.6 The old source image has to be removed");
        $this->assertFalse($this->findImage("thb-" . $avatar->getId() . ".png"), "2.7 The old thumbnail image has to be removed");

        // Test delete.
        $id = $avatarUpdated->getId();
        static::$em->remove($avatarUpdated);
        static::$em->flush();
        static::$em->clear();
        $avatarDeleted = static::$repository->find($id);
        $this->assertNull($avatarDeleted, "3.1. L'entité doit être supprimée.");
        $this->assertFalse($this->findImage("src-" . $id . ".jpg"), "3.2 The source image has to be removed");
        $this->assertFalse($this->findImage("thb-" . $id . ".jpg"), "3.3 The thumbnail image has to be removed");
    }

}
