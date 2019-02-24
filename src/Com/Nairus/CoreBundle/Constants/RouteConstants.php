<?php

namespace Com\Nairus\CoreBundle\Constants;

/**
 * Constants for all app routes name.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class RouteConstants {

    const HOMEPAGE = "ns_core_homepage";
    const CONTACT = "ns_core_contact";
    const RESUME_HOMEPAGE = "ns_resume_homepage";

    /**
     * Constructor.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {
        // this class should not be instanciated.
    }

}
