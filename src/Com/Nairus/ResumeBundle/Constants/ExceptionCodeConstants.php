<?php

namespace Com\Nairus\ResumeBundle\Constants;

/**
 * Constants for functionnal codes of bundle's exceptions.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExceptionCodeConstants {

    /**
     * Define the wrong page exception code.
     */
    public const WRONG_PAGE = 10;

    /**
     * Define the code when a page doesn't exist.
     */
    public const PAGE_NOT_FOUND = 11;

    /**
     * Define the code when an unknown error is thrown.
     */
    public const UNKNOWN_ERROR = 1;

    /**
     * Constructor.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {
        // this class should not be instanciated.
    }

}
