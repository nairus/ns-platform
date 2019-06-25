<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\ResumeBundle\Entity\SkillLevel;

/**
 * Interface of SkillLevel service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface SkillLevelServiceInterface {

    /**
     * Remove a skill level entity.
     *
     * @param SkillLevel $skillLevel The entity to remove.
     *
     * @return void
     *
     * @throws FunctionalException if an error occurs.
     */
    public function removeSkillLevel(SkillLevel $skillLevel): void;
}
