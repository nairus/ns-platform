<?php

namespace Com\Nairus\ResumeBundle\Helper;

use Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto;

/**
 * ResumeHelper interface.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ResumeHelperInterface {

    /**
     * Return <code>true</true> if the resume is complete.
     *
     * @param ResumeDetailsDto $dto The resume's details dto to check.
     *
     * @return bool
     */
    public function isComplete(ResumeDetailsDto $dto): bool;
}
