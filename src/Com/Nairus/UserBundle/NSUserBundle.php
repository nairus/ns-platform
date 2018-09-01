<?php

namespace Com\Nairus\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * User bundle.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)

 */
class NSUserBundle extends Bundle {

    const NAME = "NSUserBundle";

    /**
     * {@inheritDoc}
     */
    public function getParent() {
        return 'FOSUserBundle';
    }

}
