<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Entity\ContactMessage;
use Com\Nairus\CoreBundle\Exception\FunctionalException;

/**
 * Contact service interface.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ContactServiceInterface {

    /**
     * Error code for blacklisted error.
     */
    const IS_BLACKLITED_ERROR_CODE = 1;

    /**
     * Handle contact message and return <code>true</code> if succeed, throw a <code>FunctionalException</code> otherwise.
     *
     * @param string         $clientIp       The client ip.
     * @param ContactMessage $contactMessage The contact message.
     *
     * @return bool
     *
     * @throws FunctionalException
     */
    public function handleContactMessage(string $clientIp, ContactMessage $contactMessage): bool;
}
