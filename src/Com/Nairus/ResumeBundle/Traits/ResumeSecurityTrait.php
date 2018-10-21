<?php

namespace Com\Nairus\ResumeBundle\Traits;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Trait for resume security check.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait ResumeSecurityTrait {

    /**
     * Check if the current resume owns to the current user.
     *
     * @param Resume $resume The current resume.
     *
     * @return void
     *
     * @throws AccessDeniedException In case of security violation.
     */
    protected function check(Resume $resume, User $user): void {
        if ($user->getUsername() !== $resume->getAuthor()->getUsername()) {
            throw new AccessDeniedException();
        }
    }

}
