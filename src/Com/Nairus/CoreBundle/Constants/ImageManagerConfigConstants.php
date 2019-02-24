<?php

namespace Com\Nairus\CoreBundle\Constants;

/**
 * Constants for of ImageManager config file.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ImageManagerConfigConstants {

    /**
     * This class should not be instantiated
     *
     * @codeCoverageIgnore
     */
    private function __construct() {

    }

    /**
     * Prefixes for the image's names.
     */
    const SRC_NAME_PREFIX = "src-";
    const THB_NAME_PREFIX = "thb-";

    /**
     * Config keys.
     */
    const UPLOADS_BASE_DIR = "uploads_base_dir";
    const RELATIVE_BASE_DIR = "relative_base_dir";
    const ENTITIES = "entities";
    const SRC_WIDTH = "src_width";
    const SRC_HEIGHT = "src_height";
    const THB_WIDTH = "thb_width";
    const THB_HEIGHT = "thb_height";
    const CROP = "crop";

    /**
     * Return the required keys for the config file
     *
     * @return array
     */
    public static final function getConfigKeysRequired(): array {
        return [
            static::UPLOADS_BASE_DIR,
            static::RELATIVE_BASE_DIR,
            static::ENTITIES,
        ];
    }

    /**
     * Return the config keys required for image entity.
     *
     * @return array
     */
    public static final function getEntityConfigKeysRequired(): array {
        return [
            static::CROP,
            static::SRC_HEIGHT,
            static::SRC_WIDTH,
            static::THB_HEIGHT,
            static::THB_WIDTH,
        ];
    }

}
