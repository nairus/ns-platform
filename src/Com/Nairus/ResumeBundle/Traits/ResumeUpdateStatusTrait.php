<?php

namespace Com\Nairus\ResumeBundle\Traits;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Event\NSResumeEvents;
use Com\Nairus\ResumeBundle\Event\ResumeStatusEvent;

/**
 * Trait that dispatch event to update resume status.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait ResumeUpdateStatusTrait {

    /**
     * Instance of the event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Dipatch UPDATE_STATUS event when adding resume contents.
     *
     * @param Resume $resume The resume to update.
     *
     * @return void
     */
    protected function dispatchUpdateEvent(Resume $resume): void {
        $resumeStatusEvent = new ResumeStatusEvent($resume);
        $this->eventDispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEvent);
    }

    /**
     * Dispatch DELETE_STATUS event when deleting resume contens.
     *
     * @param Resume $resume The resume to update.
     *
     * @return void
     */
    protected function dispatchDeleteEvent(Resume $resume): void {
        $resumeStatusEvent = new ResumeStatusEvent($resume);
        $this->eventDispatcher->dispatch(NSResumeEvents::DELETE_STATUS, $resumeStatusEvent);
    }

}
