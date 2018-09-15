<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\Dto\SkillPaginatorDto;
use Com\Nairus\ResumeBundle\Entity\Skill;

/**
 * Skill service interface.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface SkillServiceInterface {

    /**
     * Find all entities for current page.
     *
     * @param int $page  The current page.
     * @param int $limit The entities limit per page.
     *
     * @return SkillPaginatorDto
     *
     * @throws <code>PaginatorException</code> in case of pagination error.
     */
    public function findAllForPage(int $page, int $limit): SkillPaginatorDto;

    /**
     * Remove a skill.
     *
     * @param Skill $skill The current skill.
     *
     * @throw <code>FunctionalException</code> if an errror occured.
     */
    public function removeSkill(Skill $skill): void;
}
