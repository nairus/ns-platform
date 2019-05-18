<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Entity as NSEntity;
use Com\Nairus\CoreBundle\Exception\FunctionalException;
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

}
