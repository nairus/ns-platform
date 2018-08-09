<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Exception\ResumeListException;
use Com\Nairus\ResumeBundle\Enums\ExceptionCodeEnums;
use Com\Nairus\ResumeBundle\Repository\ResumeRepository;
use Com\Nairus\ResumeBundle\Service\ResumeServiceInterface;
use Doctrine\ORM\EntityManager;

/**
 * Service of Resume.
 *
 * @author nairus
 */
class ResumeService implements ResumeServiceInterface
{

    /**
     *
     * @var ResumeRepository
     */
    private $resumeRespository;

    public function __construct(EntityManager $entityManager)
    {
        $this->resumeRespository = $entityManager->getRepository(NSResumeBundle::NAME . ":Resume");
    }

    /**
     * {@inheritDoc}
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage): \Doctrine\ORM\Tools\Pagination\Paginator
    {
        if ($page < 1) {
            throw new ResumeListException($page, "Wrong page", ExceptionCodeEnums::WRONG_PAGE);
        }

        $resumePaginator = $this->resumeRespository->findAllOnlineForPage($page, $nbPerPage);
        if (0 === count($resumePaginator->getQuery()->getResult())) {
            throw new ResumeListException($page, "Page not found", ExceptionCodeEnums::PAGE_NOT_FOUND);
        }

        return $resumePaginator;
    }

}
