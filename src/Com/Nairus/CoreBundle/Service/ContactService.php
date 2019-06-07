<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Dto\ContactMessageDto;
use Com\Nairus\CoreBundle\Entity as NSEntity;
use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Exception\PaginatorException;
use Com\Nairus\CoreBundle\Repository as NSRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Contact service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactService implements ContactServiceInterface {

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BlacklistedIp repository;
     *
     * @var NSRepository\BlacklistedIpRepository
     */
    private $blacklistedIpRepository;

    /**
     * ContactMessage repository.
     *
     * @var NSRepository\ContactMessageRepository
     */
    private $contactMessageRepository;

    /**
     * The logger interface.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param LoggerInterface        $logger        The logger service.
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger) {
        $this->entityManager = $entityManager;
        $this->blacklistedIpRepository = $entityManager->getRepository(NSEntity\BlacklistedIp::class);
        $this->contactMessageRepository = $entityManager->getRepository(NSEntity\ContactMessage::class);
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function handleContactMessage(string $clientIp, NSEntity\ContactMessage $contactMessage): bool {
        if ($this->blacklistedIpRepository->isBlackListed($clientIp)) {
            throw new FunctionalException("contact.message.success", "The ip is blacklisted", static::IS_BLACKLITED_ERROR_CODE);
        }

        $this->entityManager->beginTransaction();
        try {
            $contactMessage->setIp($clientIp)
                    ->setRequestDate(new \DateTimeImmutable());
            $this->entityManager->persist($contactMessage);
            $this->entityManager->flush();
            $this->entityManager->clear(NSEntity\ContactMessage::class);
            $this->entityManager->commit();
            return true;
        } catch (\Throwable $exc) {
            $this->logger->error("An error occured while handle a contact message (ip: {clientIp} / message: {message} / error: {error})",
                    ["clientIp" => $clientIp, "message" => $contactMessage->getMessage(), "error" => $exc->getMessage()]);
            $this->entityManager->rollback();
            throw new FunctionalException("contact.message.error", "An error occured", 0, $exc);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findAllForPage(int $page, int $limit): ContactMessageDto {
        // Bad page argument
        if ($page < 1) {
            // Throws an exception.
            throw new PaginatorException($page, "Bad page [$page] for new list");
        }

        // Calculate the offset for the current page.
        $offset = ($page - 1) * $limit;
        $contactMessagePaginator = $this->contactMessageRepository->findAllForPage($offset, $limit);
        $entities = $contactMessagePaginator->getIterator()->getArrayCopy();

        // Calculate the number of pages.
        $total = $contactMessagePaginator->count();
        $pages = ceil($total / $limit);

        // Build and return the dto.
        $contactMessageDto = new ContactMessageDto();
        $contactMessageDto->setCurrentPage($page)
                ->setEntities($entities)
                ->setBlacklistedIps($this->buildBlacklistedIdMap($entities))
                ->setPages($pages);
        return $contactMessageDto;
    }

    /**
     * {@inheritDoc}
     */
    public function blacklistContactMessage(NSEntity\ContactMessage $contactMessage): bool {
        if (!$this->blacklistedIpRepository->isBlackListed($contactMessage->getIp())) {
            $blacklistedIp = new NSEntity\BlacklistedIp();
            $blacklistedIp->setIp($contactMessage->getIp());
            $this->entityManager->persist($blacklistedIp);
            $this->entityManager->flush();
            $this->entityManager->clear(NSEntity\BlacklistedIp::class);
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteContactMessage(NSEntity\ContactMessage $contactMessage): int {
        $id = $contactMessage->getId();
        $this->entityManager->remove($contactMessage);
        $this->entityManager->flush();
        $this->entityManager->clear(NSEntity\ContactMessage::class);
        return $id;
    }

    /**
     * Build the blacklistedId map from entities collection.
     *
     * @param array $entities The current entities collection.
     *
     * @return array
     */
    private function buildBlacklistedIdMap(array $entities): array {
        // Get the blacklisted ips from current collection.
        $contactMessageIps = [];
        foreach ($entities as /* @var $entity NSEntity\ContactMessage */ $entity) {
            array_push($contactMessageIps, $entity->getIp());
        }

        // build the blacklisted ip map.
        $blacklistedIps = $this->blacklistedIpRepository->findByIp(array_unique($contactMessageIps));
        $blacklistedIpsMap = [];
        foreach ($blacklistedIps as /* @var $blacklistedIp NSEntity\BlacklistedIp */ $blacklistedIp) {
            $blacklistedIpsMap[$blacklistedIp->getIp()] = $blacklistedIp->getBlacklistedAt();
        }

        return $blacklistedIpsMap;
    }

}
