<?php

namespace Com\Nairus\ResumeBundle\Helper;

use Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto;

/**
 * Implementation of the resume helper.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeHelper implements ResumeHelperInterface {

    /**
     * {@inheritDoc}
     */
    public function isComplete(ResumeDetailsDto $dto): bool {
        if (!$dto->isAnonymous() && null === $dto->getProfile()) {
            return false;
        }

        if (0 === $dto->getEducations()->count()) {
            return false;
        }

        if (0 === $dto->getExperiences()->count()) {
            return false;
        }

        if (0 === $dto->getResumeSkills()->count()) {
            return false;
        }

        return true;
    }

}
