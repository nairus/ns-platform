<?php

namespace Com\Nairus\ResumeBundle\Listener;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Event\NSResumeEvents;
use Com\Nairus\ResumeBundle\Event\ResumeStatusEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Listener for updating a resume status.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeStatusListener implements EventSubscriberInterface {

    /**
     * The resume repository instance.
     *
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * The constructor.
     *
     * @param ObjectManager $entityManager The entity manager.
     */
    public function __construct(ObjectManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array {
        return [
            NSResumeEvents::UPDATE_STATUS,
            NSResumeEvents::DELETE_STATUS
        ];
    }

    /**
     * Update the status when adding a content.
     *
     * @param ResumeStatusEvent $event The event dispatched.
     *
     * @return void
     */
    public function onUpdateStatus(ResumeStatusEvent $event): void {
        $resume = $event->getResume();

        // If the status is not equal to OFFLINE_INCOMPLETE, we do nothing.
        if ($resume->getStatus() !== ResumeStatusEnum::OFFLINE_INCOMPLETE) {
            return;
        }

        // If no education is added, we do nothing.
        if ($resume->getEducations()->count() === 0) {
            return;
        }

        // If no experience is added, we do nothing.
        if ($resume->getExperiences()->count() === 0) {
            return;
        }

        // If no resume skill is added, we do nothing
        if ($resume->getResumeSkills()->count() === 0) {
            return;
        }

        // If the resume is OFFLINE_INCOMPLETE and all dependencies has been added
        $this->updateStatus($resume->getId(), ResumeStatusEnum::OFFLINE_TO_PUBLISHED);
    }

    /**
     * Update the resume status when deleting a content.
     *
     * @param ResumeStatusEvent $event The event dispatched.
     *
     * @return void
     */
    public function onDeleteStatus(ResumeStatusEvent $event): void {
        $resume = $event->getResume();

        // If the status is equal to ONLINE, we do nothing.
        if ($resume->getStatus() === ResumeStatusEnum::ONLINE) {
            return;
        }

        // If no education is added, we downgrade the status.
        if ($resume->getEducations()->count() === 0) {
            $this->updateStatus($resume->getId(), ResumeStatusEnum::OFFLINE_INCOMPLETE);
            return;
        }

        // If no experience is added, we downgrade the status.
        if ($resume->getExperiences()->count() === 0) {
            $this->updateStatus($resume->getId(), ResumeStatusEnum::OFFLINE_INCOMPLETE);
            return;
        }

        // If no resume skill is added, we downgrade the status.
        if ($resume->getResumeSkills()->count() === 0) {
            $this->updateStatus($resume->getId(), ResumeStatusEnum::OFFLINE_INCOMPLETE);
            return;
        }
    }

    /**
     * Update the resume status.
     *
     * @param int    $resumeId The current resume id.
     * @param string $status   The status to update.
     *
     * @return void
     */
    private function updateStatus(int $resumeId, string $status): void {
        // Refresh the entity if necessary (avoid InvalidArgumentException: Entity has to be managed or scheduled for removal for single computation).
        $resume = $this->entityManager->getRepository(Resume::class)->find($resumeId);
        // Set the new status.
        $resume->setStatus($status);
        // Update the status.
        $this->entityManager->flush($resume);
    }

}
