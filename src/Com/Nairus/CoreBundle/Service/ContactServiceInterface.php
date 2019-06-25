<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Dto\ContactMessageDto;
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
     * Handle contact message and return `true` if succeed, throw a `FunctionalException` otherwise.
     *
     * @param string         $clientIp       The client ip.
     * @param ContactMessage $contactMessage The contact message.
     *
     * @return bool
     *
     * @throws FunctionalException
     */
    public function handleContactMessage(string $clientIp, ContactMessage $contactMessage): bool;

    /**
     * Find contact message for current page.
     *
     * @param int $page  The current page.
     * @param int $limit The limit of entities per page.
     *
     * @return ContactMessageDto
     */
    public function findAllForPage(int $page, int $limit): ContactMessageDto;

    /**
     * Delete a contact message and return the entity's id deleted if succeed.
     *
     * @param ContactMessage $contactMessage The entity to delete.
     *
     * @return int
     */
    public function deleteContactMessage(ContactMessage $contactMessage): int;

    /**
     * Blacklist a contact message ip and return `true` if succeed.
     *
     * @param ContactMessage $contactMessage The entity to blacklist.
     *
     * @return bool
     */
    public function blacklistContactMessage(ContactMessage $contactMessage): bool;
}
