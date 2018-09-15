<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Repository\ResumeSkillRepository;
use Com\Nairus\ResumeBundle\Repository\SkillLevelRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementation of SkillLevel service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelService implements SkillLevelServiceInterface {

    /**
     * @var SkillLevelRepository
     */
    private $skillLevelRepository;

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
        $this->skillLevelRepository = $entityManager->getRepository(NSResumeBundle::NAME . ":SkillLevel");
        $this->resumeSkillRepository = $entityManager->getRepository(NSResumeBundle::NAME . ":ResumeSkill");
    }

    /**
     * {@inheritDoc}
     */
    public function removeSkillLevel(SkillLevel $skillLevel): void {
        // Check if no resume is linked to this skillLevel.
        $resumeSkill = $this->resumeSkillRepository->findOneBy(["skillLevel" => $skillLevel]);

        if (null !== $resumeSkill) {
            throw new FunctionalException(
                    "flashes.error.skill-level.delete.resume-linked",
                    "A resume is linked to the skill level No. " . $skillLevel->getId());
        }

        try {
            $this->skillLevelRepository->remove($skillLevel);
        } catch (\Doctrine\ORM\ORMException | \Doctrine\ORM\ORMInvalidArgumentException $exc) {
            $skillLevelId = $skillLevel->getId();
            throw new FunctionalException(
                    "flashes.error.skill-level.delete.failed",
                    "The skill level No. $skillLevelId has not been deleted!", 0, $exc);
        } catch (\Exception | \Error $exc) {
            throw new FunctionalException(
                    "flashes.error.unknown",
                    "An unkwnow error occured", 0, $exc);
        }
    }

}
