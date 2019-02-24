<?php

namespace Com\Nairus\CoreBundle\Manager;

use Com\Nairus\CoreBundle\Constants\ImageManagerConfigConstants;
use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Com\Nairus\CoreBundle\Entity\ImageInterface;
use Com\Nairus\CoreBundle\Exception\KeyMissingException;
use Com\Nairus\CoreBundle\Exception\ImageProcessingException;
use Com\Nairus\CoreBundle\Util\StringUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Implementation of ImageManager with gd library.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GDImageManager implements ImageManagerInterface {

    /**
     * The image manager configuration.
     *
     * @var array
     */
    private $config;

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param array           $config The component configuration.
     * @param LoggerInterface $logger The logger instance.
     */
    public function __construct(array $config, LoggerInterface $logger) {
        // Set the config.
        $this->config = $config;
        // Set the logger.
        $this->logger = $logger;
        // Verify if the config is well formed.
        $this->checkConfigKeys(ImageManagerConfigConstants::getConfigKeysRequired());
    }

    /**
     * {@inheritDoc}
     */
    public function getExtraFolders(ImageInterface $imageEntity): string {
        // Get the two last digits of the id.
        $id = (string) $imageEntity->getId();
        $lastDigit = substr($id, -1, 1);
        if (strlen($id) > 1) {
            $firstDigit = substr($id, -2, 1);
        } else {
            $firstDigit = 0;
        }

        return sprintf("%d%s%d%s", $firstDigit, DIRECTORY_SEPARATOR, $lastDigit, DIRECTORY_SEPARATOR);
    }

    /**
     * {@inheritDoc}
     */
    public function buildRelativePath(ImageInterface $imageEntity): void {
        $relativePath = sprintf("%s%s%s",
                $this->config[ImageManagerConfigConstants::RELATIVE_BASE_DIR],
                $this->getShortName($imageEntity),
                DIRECTORY_SEPARATOR
        );

        $imageEntity->setRelativePath($relativePath);
    }

    /**
     * {@inheritDoc}
     */
    public function crop(File $source, string $target, int $width, int $height): bool {
        // try to get image resource to process
        list($image, $srcWidth, $srcHeight) = $this->getImageDatas($source->getPathname(), $source->getMimeType(), $width, $height);

        // calculate the x position
        $x = ($srcWidth - $width) / 2;
        $y = ($srcHeight - $height) / 2;

        $rect = ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height];
        $imageCropped = imagecrop($image, $rect);

        if (false === $imageCropped) {
            $this->logger->error("An error occured while cropping the image: {target}", ["target" => $target]);
            throw new ImageProcessingException("An error occured while processing the image.", ImageProcessingException::CROP_ERROR);
        }

        // free the resources.
        imagedestroy($image);

        return $this->saveImage($imageCropped, $target, $source->getMimeType());
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(ImageInterface $imageEntity): ImageConfigDto {
        // Verify the entity
        $shortName = $this->getShortName($imageEntity);
        $this->checkConfigKeys(ImageManagerConfigConstants::getEntityConfigKeysRequired(), $shortName);

        // Build the DTO if the config is valid.
        $entityConfig = $this->config[ImageManagerConfigConstants::ENTITIES][$shortName];
        $imageConfigDto = new ImageConfigDto();

        // Set the common properties.
        $imageConfigDto->setBaseUploadDir($this->config[ImageManagerConfigConstants::UPLOADS_BASE_DIR])
                ->setRelativeBaseDir($this->config[ImageManagerConfigConstants::RELATIVE_BASE_DIR]);

        // Set the entity properties.
        foreach ($entityConfig as $key => $value) {
            $methodName = "set" . StringUtil::camelize($key);
            $imageConfigDto->$methodName($value);
        }

        return $imageConfigDto;
    }

    /**
     * {@inheritDoc}
     */
    public function resize(File $source, string $target, int $width, int $height): bool {
        // try to get image resource to process
        list($image, $srcWidth, $srcHeight) = $this->getImageDatas($source->getPathname(), $source->getMimeType(), $width, $height);

        // caculate the original ratio
        $originalRatio = $srcWidth / $srcHeight;

        if ($width / $height > $originalRatio) {
            $width = $height * $originalRatio;
        } else {
            $height = $width / $originalRatio;
        }

        // resizing the image
        $imageTrueColor = imagecreatetruecolor($width, $height);
        if (false === $imageTrueColor) {
            $this->logger->error("An error occured with [imagecreatetruecolor] function for file: {target}", ["target" => $target]);
            throw new ImageProcessingException("An error occured while processing the image with [imagecreatetruecolor] function.", ImageProcessingException::RESIZE_ERROR);
        }

        if (false === imagecopyresampled($imageTrueColor, $image, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight)) {
            $this->logger->error("An error occured with [imagecopyresampled] function for file: {target}", ["target" => $target]);
            throw new ImageProcessingException("An error occured while processing the image with [imagecopyresampled] function.", ImageProcessingException::RESIZE_ERROR);
        }

        // free the resources.
        imagedestroy($image);

        return $this->saveImage($imageTrueColor, $target, $source->getMimeType());
    }

    /**
     * Check config keys required.
     *
     * @param array       $keys   The config keys required.
     * @param string|bool $entity The name decalize of the entity or <code>false</code>.
     *
     * @return void
     *
     * @throws KeyMissingException
     */
    private function checkConfigKeys(array $keys, $entity = false): void {
        $config = $this->config;
        if ($entity) {
            if (!array_key_exists($entity, $this->config[ImageManagerConfigConstants::ENTITIES])) {
                $this->logger->error("The key [{key}] is missing.", ["key" => $entity]);
                throw new KeyMissingException(sprintf("The key [%s] is missing.", $entity));
            }
            $config = $this->config[ImageManagerConfigConstants::ENTITIES][$entity];
        }


        foreach ($keys as $key) {
            if (!array_key_exists($key, $config)) {
                $this->logger->error("The key [{key}] is missing.", ["key" => $key]);
                throw new KeyMissingException(sprintf("The key [%s] is missing.", $key));
            }
        }
    }

    /**
     * Return the short name decamelize of the image entity.
     *
     * @param ImageInterface $imageEntity The image entity.
     *
     * @return string
     */
    private function getShortName(ImageInterface $imageEntity): string {
        // Get the short name of the instance class.
        $shortName = (new \ReflectionClass($imageEntity))->getShortName();

        return StringUtil::decamelize($shortName);
    }

    /**
     * Return the image datas to process or <code>false</code> if an error occurs.
     *
     * @param string $imagePathName The image path to process.
     * @param string $mimeType      The image mimeType.
     * @param string $targetWidth   The target image width.
     * @param string $targetHeight  The target image height.
     *
     * @return array
     *
     * @throws ImageProcessingException if the creation of the image to process fails.
     */
    private function getImageDatas(string $imagePathName, string $mimeType, int $targetWidth, int $targetHeight): array {
        // get the image information
        list($srcWidth, $srcHeight) = getimagesize($imagePathName);

        // If the image is smaller than the configuration.
        if ($srcWidth < $targetWidth || $srcHeight < $targetHeight) {
            $this->logger->error("The image is smaller than the target values: {srcWidth}x{srcHeight}px => {targetWidth}x{targetHeight}px", [
                "srcWidth" => $srcWidth, "srcHeight" => $srcHeight,
                "targetHeight" => $targetHeight, "targetWidth" => $targetWidth]);
            throw new ImageProcessingException("The image is smaller than the target values.", ImageProcessingException::GET_IMAGE_DATAS_ERROR);
        }

        $imageToProcess = false;
        switch ($mimeType) {
            case "image/jpeg":
                $imageToProcess = imagecreatefromjpeg($imagePathName);
                break;
            case "image/gif":
                $imageToProcess = imagecreatefromgif($imagePathName);
                break;
            case "image/png";
                $imageToProcess = imagecreatefrompng($imagePathName);
                break;
            default:
                break;
        }

        if (false === $imageToProcess) {
            $this->logger->error("An error occured during creation of the image to process: {imagePathName}", ["imagePathName" => $imagePathName]);
            throw new ImageProcessingException("An error occured during creation of the image to process.", ImageProcessingException::GET_IMAGE_DATAS_ERROR);
        }

        return [$imageToProcess, $srcWidth, $srcHeight];
    }

    /**
     * Save the image processed.
     *
     * @param resource $imageToSave The image processed to save.
     * @param string   $target      The target filepath.
     * @param string   $mimeType    The image mimeType.
     *
     * @return bool
     */
    private function saveImage($imageToSave, string $target, string $mimeType): bool {
        // Note: at this step, we expect only those three mimeTypes.
        if ("image/jpeg" === $mimeType) {
            $saved = imagejpeg($imageToSave, $target, 70);
        } elseif ("image/gif" === $mimeType) {
            $saved = imagegif($imageToSave, $target);
        } else {
            $saved = imagepng($imageToSave, $target, 4);
        }

        // free the resources.
        imagedestroy($imageToSave);

        return false !== $saved;
    }

}
