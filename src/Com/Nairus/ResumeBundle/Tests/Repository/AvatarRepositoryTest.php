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
        $this->assertEquals("image-to-resize.png", $avatar->getOriginalName(), "1.6 The original image name has to be saved");

        // Test update.
        $expectedRelativePath = $avatar->getRelativePath();

        // Test update with same extension.
        $imagePath = static::$projectDirectory . $DS . "tests" . $DS . "resources" . $DS . "nairus.png";
        $uploadedFile = new UploadedFile($imagePath, "nairus.png");
        // NOTE: we need to update [originalName] field manually to dispatch update event.
        $avatar
                ->setImageFile($uploadedFile)
                ->setOriginalName("nairus.png");
        static::$em->flush();
        static::$em->clear();

        /* @var $avatarUpdated Avatar */
        $avatarUpdated = static::$repository->find($avatar->getId());
        $this->assertSame("png", $avatarUpdated->getExtension(), "2.1. The [extension] field has to be remain identical.");
        $this->assertSame($expectedRelativePath, $avatarUpdated->getRelativePath(), "2.2. The [relativeField] field has to remain identical.");
        $this->assertTrue($this->findImage("src-" . $avatar->getId() . ".png"), "2.4 The new source image has to be created");
        $this->assertTrue($this->findImage("thb-" . $avatar->getId() . ".png"), "2.5 The new thumbnail image has to be created");
        $this->assertEquals("nairus.png", $avatarUpdated->getOriginalName(), "2.6 The original name has to be updated");

        // Test update with another extension.
        $imagePath = static::$projectDirectory . $DS . "tests" . $DS . "resources" . $DS . "image-to-resize.jpg";
        $avatarUpdated
                ->setImageFile(new UploadedFile($imagePath, "image-to-resize.jpg"))
                ->setOriginalName("image-to-resize.jpg");
        static::$em->flush();
        static::$em->clear();

        /* @var $avatarUpdated Avatar */
        $avatarWithOtherExtension = static::$repository->find($avatarUpdated->getId());
        $this->assertSame("jpg", $avatarWithOtherExtension->getExtension(), "3.1. The [extension] field has to be changed.");
        $this->assertSame($expectedRelativePath, $avatarWithOtherExtension->getRelativePath(), "3.2. The [relativeField] field has to remain identical.");
        $this->assertTrue($this->findImage("src-" . $avatar->getId() . ".jpg"), "3.4 The new source image has to be created");
        $this->assertTrue($this->findImage("thb-" . $avatar->getId() . ".jpg"), "3.5 The new thumbnail image has to be created");
        $this->assertFalse($this->findImage("src-" . $avatar->getId() . ".png"), "3.6 The old source image has to be removed");
        $this->assertFalse($this->findImage("thb-" . $avatar->getId() . ".png"), "3.7 The old thumbnail image has to be removed");
        $this->assertEquals("image-to-resize.jpg", $avatarWithOtherExtension->getOriginalName(), "3.8 The original name has to be updated");

        // Test delete.
        $id = $avatarWithOtherExtension->getId();
        static::$em->remove($avatarWithOtherExtension);
        static::$em->flush();
        static::$em->clear();
        $avatarDeleted = static::$repository->find($id);
        $this->assertNull($avatarDeleted, "4.1. L'entité doit être supprimée.");
        $this->assertFalse($this->findImage("src-" . $id . ".jpg"), "4.2 The source image has to be removed");
        $this->assertFalse($this->findImage("thb-" . $id . ".jpg"), "4.3 The thumbnail image has to be removed");
    }

}
