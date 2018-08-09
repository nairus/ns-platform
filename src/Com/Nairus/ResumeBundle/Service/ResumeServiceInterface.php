<?php

namespace Com\Nairus\ResumeBundle\Service;

/**
 * Interface of the resume service.
 *
 * @author nairus
 */
interface ResumeServiceInterface
{

    /**
     * Return a collection of ResumeListDto
     *
     * @param int $page      The current page.
     * @param int $nbPerPage The number of elements per page.
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     *
     * @throws \Com\Nairus\ResumeBundle\Exception\ResumeListException When an error occurs.
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage) : \Doctrine\ORM\Tools\Pagination\Paginator;

}
