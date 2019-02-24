<?php

namespace Com\Nairus\CoreBundle\Listener;

use Com\Nairus\CoreBundle\Constants\ImageManagerConfigConstants;
use Com\Nairus\CoreBundle\Entity\ImageInterface;
use Com\Nairus\CoreBundle\Exception\ImageProcessingException;
use Com\Nairus\CoreBundle\Manager\ImageManagerInterface;
use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Listener for ImageEntity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ImageEntityListener implements EventSubscriber {

    /**
     * Image manager instance.
     *
     * @var ImageManagerInterface
     */
    private $imageManager;

    /**
     * The entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     *
     * @var
     */
    private $logger;

    /**
     * Contructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param LoggerInterface        $logger        The logger instance.
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Define the image manager (used by the container).
     *
     * @param ImageManagerInterface  $imageManager  The image manager instance.
     *
     * @return void
     */
    public function setImageManager(ImageManagerInterface $imageManager): void {
        $this->imageManager = $imageManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array {
        return [Events::prePersist, Events::preRemove, Events::postPersist, Events::postUpdate];
    }

    /**
     * Invoked before persist an entity.
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     *
     * @throws ImageProcessingException if there is no image to process
     */
    public function prePersist(LifecycleEventArgs $args): void {
        $imageEntity = $this->checkEntity($args, true);
        if (!$imageEntity) {
            return;
        }

        // build the relative path and set the extension
        $this->imageManager->buildRelativePath($imageEntity);
        $imageEntity->setExtension($imageEntity->getImageFile()->getExtension());
    }

    /**
     * Invoked after persist an entity.
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     *
     * @throws ImageProcessingException if there is no image to process
     */
    public function postPersist(LifecycleEventArgs $args): void {
        $imageEntity = $this->checkEntity($args, true);
        if (!$imageEntity) {
            return;
        }

        // update the final relative path for the image processing.
        $imageEntity->setRelativePath($imageEntity->getRelativePath() . $this->imageManager->getExtraFolders($imageEntity));
        $repository = $this->entityManager->getRepository((new \ReflectionClass($imageEntity))->getName());
        /* @var $refreshedEntity ImageInterface */
        $refreshedEntity = $repository->find($imageEntity->getId());
        $refreshedEntity
                ->setRelativePath($imageEntity->getRelativePath())
                ->setImageFile($imageEntity->getImageFile());
        $this->entityManager->flush();
    }

    /**
     * Invoked before remove the entity.
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function preRemove(LifecycleEventArgs $args): void {
        $imageEntity = $this->checkEntity($args);
        if (!$imageEntity) {
            return;
        }

        // get the entity config
        $config = $this->imageManager->getConfig($imageEntity);
        $suffix = $imageEntity->getId() . "." . $imageEntity->getExtension();

        // try to remove the source file
        $this->deleteImageFile($imageEntity, $config, ImageManagerConfigConstants::SRC_NAME_PREFIX . $suffix);

        // then try to remove the thumbnail
        $this->deleteImageFile($imageEntity, $config, ImageManagerConfigConstants::THB_NAME_PREFIX . $suffix);
    }

    /**
     * Invoked after update an entity.
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     *
     * @throws ImageProcessingException if an error occurs while processing the image file.
     */
    public function postUpdate(LifecycleEventArgs $args): void {
        $success = false;
        try {
            // we do nothing if this is not an Image entity or if there is no image to update.
            $imageEntity = $this->checkEntity($args, true);
            if (!$imageEntity) {
                return;
            }

            // Get the entity config.
            $config = $this->imageManager->getConfig($imageEntity);

            // Then process the new image
            $success = $this->processImage($args->getObject(), $config);
        } catch (ImageProcessingException $exc) {
            // log and rethrow the error.
            $this->logger->error("An error occured while updating the image entity: {message}", [
                "message" => $exc->getMessage()
            ]);
            throw $exc;
        } finally {
            // We try to delete old images only if the update succeed and a temporary extension was stored (before update).
            if ($success && $imageEntity->getTmpExtension()) {
                try {
                    // Then delete the old images first.
                    $this->deleteImageFile($imageEntity, $config, ImageManagerConfigConstants::SRC_NAME_PREFIX . $imageEntity->getId() . "." . $imageEntity->getTmpExtension());
                    $this->deleteImageFile($imageEntity, $config, ImageManagerConfigConstants::THB_NAME_PREFIX . $imageEntity->getId() . "." . $imageEntity->getTmpExtension());
                } catch (ImageProcessingException $exc) {
                    // log the error only.
                    $this->logger->warning("An error occured while deleting old images: {message}", [
                        "message" => $exc->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Check the type of the entity.
     *
     * @param LifecycleEventArgs $args              Entities lifecycle event args.
     * @param bool               $imageFileRequired Check if there is an uploaded file to process (default: false).
     *
     * @return ImageInterface|null image entity expected type, <code>null</code> otherwise.
     *
     * @throws ImageProcessingException if there is no image to process.
     */
    private function checkEntity(LifecycleEventArgs $args, $imageFileRequired = false): ?ImageInterface {
        $imageEntity = $args->getObject();
        if (!$imageEntity instanceof ImageInterface) {
            return null;
        }

        // If there is no required image to process.
        if ($imageFileRequired && !$imageEntity->getImageFile()) {
            throw new ImageProcessingException("[ImageListener] No image to process.", ImageProcessingException::NO_IMAGE_TO_PROCESS_ERROR);
        }

        return $imageEntity;
    }

    /**
     * Process an image.
     *
     * @param ImageInterface $imageEntity The image entity to process.
     *
     * @return bool
     */
    private function processImage(ImageInterface $imageEntity, ImageConfigDto $config): bool {
        // first create the image folder if doesn't exist.
        $baseDir = $config->getBaseUploadDir() . $imageEntity->getRelativePath();

        if (!is_dir($baseDir)) {
            // read/write for owner and group, read for everyone else.
            mkdir($baseDir, 0664, true);
        }

        // secondly create the source image.
        $srcImagePath = $baseDir . ImageManagerConfigConstants::SRC_NAME_PREFIX . $imageEntity->getId() . "." . $imageEntity->getExtension();
        $result = $this->imageManager->resize($imageEntity->getImageFile(), $srcImagePath, $config->getSrcWidth(), $config->getSrcHeight());

        // if resize succeed, we create a file object to continue the process.
        // note: on this step there is no need to check the path.
        $srcImageFile = new File($srcImagePath, false);

        // then crop the source image if we have to.
        if ($config->getCrop()) {
            $this->imageManager->crop($srcImageFile, $srcImagePath, $config->getSrcWidth(), $config->getSrcHeight());
        }

        // finally create the thumbnail with the resized source file.
        $thbImagePath = $baseDir . ImageManagerConfigConstants::THB_NAME_PREFIX . $imageEntity->getId() . "." . $imageEntity->getExtension();
        $result = $this->imageManager->resize($srcImageFile, $thbImagePath, $config->getThbWidth(), $config->getThbHeight());
        return $result;
    }

    /**
     * Delete the image file.
     *
     * @param ImageInterface $imageEntity The image entity.
     * @param ImageConfigDto $config      The image config.
     * @param string $imageName           The image name to delete.
     *
     * @return void
     *
     * @throws ImageProcessingException if an error occurs while deleting the image file.
     */
    private function deleteImageFile(ImageInterface $imageEntity, ImageConfigDto $config, string $imageName): void {
        $baseImagePath = $config->getBaseUploadDir() . $imageEntity->getRelativePath();
        try {
            unlink($baseImagePath . $imageName);
        } catch (\Throwable $exc) {
            $this->logger->error("An error occurs while removing the image (path: {imagePath})", [
                "imagePath" => $baseImagePath . $imageName,
            ]);

            throw new ImageProcessingException("An error occurs while removing the image [" . $imageName . "]", ImageProcessingException::DELETE_IMAGE_ERROR, $exc);
        }
    }

}
