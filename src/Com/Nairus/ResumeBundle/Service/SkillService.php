<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Exception\PaginatorException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Dto\SkillPaginatorDto;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository;
use Com\Nairus\ResumeBundle\Repository\SkillRepository;
use Com\Nairus\ResumeBundle\Service\SkillServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for Skill entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillService implements SkillServiceInterface {

    /**
     * @var SkillRepository
     */
    private $skillRepository;

    /**
     * @var ResumeSkillRepository
     */
    private $resumeSkillRepository;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->skillRepository = $entityManager->getRepository(NSResumeBundle::NAME . ":Skill");
        $this->resumeSkillRepository = $entityManager->getRepository(NSResumeBundle::NAME . ":ResumeSkill");
    }

    /**
     * {@inheritDoc}
     */
    public function findAllForPage(int $page, int $limit): SkillPaginatorDto {
        $skillPaginatorDto = new SkillPaginatorDto();

        // Bad page argument
        if ($page < 1) {
            // Throws an exception.
            throw new PaginatorException($page, "Bad page [$page] for new list");
        }

        // Calculate the offset for the current page.
        $offset = ($page - 1) * $limit;

        // Get the doctrine paginator.
        $skillsPaginator = $this->skillRepository->findAllForPage($offset, $limit);

        // Count the total of entities in the database.
        $total = $skillsPaginator->count();

        // Calculate the number of pages.
        $pages = ceil($total / $limit);

        // Get the entities for the page.
        $entities = $skillsPaginator->getIterator()->getArrayCopy();

        // Populate the datas for the DTO.
        $skillPaginatorDto->setCurrentPage($page)
                ->setEntities($entities)
                ->setPages($pages);

        return $skillPaginatorDto;
    }

    /**
     * {@inheritDoc}
     */
    public function removeSkill(Skill $skill) {
        // Check if no resume is linked to this skill.
        $resumeSkill = $this->resumeSkillRepository->findOneBy(["skill" => $skill]);

        if (null !== $resumeSkill) {
            throw new FunctionalException(
                    "flashes.error.skill.delete.resume-linked",
                    "A resume is linked to the skill No. " . $skill->getId());
        }

        try {
            $this->skillRepository->remove($skill);
        } catch (\Doctrine\ORM\ORMException | \Doctrine\ORM\ORMInvalidArgumentException $exc) {
            throw new FunctionalException(
                    "flashes.error.skill.delete.unknown",
                    "An unkwnow error occured", 0, $exc);
        }
    }

}
