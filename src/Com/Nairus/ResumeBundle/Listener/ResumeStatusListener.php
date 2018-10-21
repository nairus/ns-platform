<?php

namespace Com\Nairus\ResumeBundle\Listener;

use Com\Nairus\ResumeBundle\Event\NSResumeEvents;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
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
            NSResumeEvents::UPDATE_STATUS
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
        $resume->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED);
        // Update the status.
        $this->entityManager->flush($resume);
    }

}
