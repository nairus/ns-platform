<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\ResumeBundle\Dto\SkillPaginatorDto;

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
     * @throws PaginatorException
     */
    public function findAllForPage(int $page, int $limit): SkillPaginatorDto;
}
