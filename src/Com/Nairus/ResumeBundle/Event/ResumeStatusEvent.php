<?php

namespace Com\Nairus\ResumeBundle\Event;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event dispatched to udpdate a resume status.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeStatusEvent extends Event {

    /**
     * Resume entity to update.
     *
     * @var Resume
     */
    private $resume;

    /**
     * Constructor.
     *
     * @param Resume $resume The resume to update.
     */
    public function __construct(Resume $resume) {
        $this->resume = $resume;
    }

    /**
     * Return the resume to update.
     *
     * @return Resume the resume to update.
     */
    public function getResume(): Resume {
        return $this->resume;
    }

}
