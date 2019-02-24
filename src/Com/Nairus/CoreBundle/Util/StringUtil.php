<?php

namespace Com\Nairus\CoreBundle\Util;

/**
 * Implementation of string util.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class StringUtil {

    /**
     * This class should not be instantiated.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {

    }

    /**
     * {@inheritDoc}
     */
    public static function camelize(string $string): string {
        return preg_replace("~[^a-z0-9]~i", "", ucwords($string, "_"));
    }

    /**
     * {@inheritDoc}
     */
    public static function decamelize(string $string): string {
        return strtolower(
                preg_replace(
                        ['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2',
                        $string)
        );
    }

}
