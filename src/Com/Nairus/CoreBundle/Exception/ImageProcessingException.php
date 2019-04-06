<?php

namespace Com\Nairus\CoreBundle\Exception;

/**
 * Exception thrown when there is an error while image processing.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ImageProcessingException extends \Exception {

    /**
     * Define the global error codes.
     */
    const CROP_ERROR = 1;
    const RESIZE_ERROR = 2;
    const GET_IMAGE_DATAS_ERROR = 3;
    const NO_IMAGE_TO_PROCESS_ERROR = 4;
    const DELETE_IMAGE_ERROR = 5;
    const INVALID_MIME_TYPE = 6;

}
