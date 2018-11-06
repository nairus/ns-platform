<?php

namespace Com\Nairus\ResumeBundle\Event;

/**
 * Events list of the bundle.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class NSResumeEvents {

    /**
     * The UPDATE_STATUS event occurs when the user add a new informations (Education, Experience, ResumeSkill) for a resume.
     */
    const UPDATE_STATUS = 'nsresume.update.resume.status';

    /**
     * The DELETE_STATUS event occurs when the user delete an informations (Education, Experience, ResumeSkill) for a resume.
     */
    const DELETE_STATUS = 'nsresume.delete.resume.status';

}
