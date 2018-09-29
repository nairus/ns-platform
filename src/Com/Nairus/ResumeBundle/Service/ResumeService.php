<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Exception as NSResumeException;
use Com\Nairus\ResumeBundle\Enums\ExceptionCodeEnums;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Repository\ResumeRepository;
use Com\Nairus\ResumeBundle\Service\ResumeServiceInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Service of Resume.
 *
 * @author nairus
 */
class ResumeService implements ResumeServiceInterface {

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var ResumeRepository
     */
    private $resumeRepository;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager The current entity manager.
     */
    public function __construct(ObjectManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->resumeRepository = $entityManager->getRepository(NSResumeBundle::NAME . ":Resume");
    }

    /**
     * {@inheritDoc}
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage): \Doctrine\ORM\Tools\Pagination\Paginator {
        if ($page < 1) {
            throw new NSResumeException\ResumeListException($page, "Wrong page", ExceptionCodeEnums::WRONG_PAGE);
        }

        $resumePaginator = $this->resumeRepository->findAllOnlineForPage($page, $nbPerPage);
        if (0 === count($resumePaginator->getQuery()->getResult())) {
            throw new NSResumeException\ResumeListException($page, "Page not found", ExceptionCodeEnums::PAGE_NOT_FOUND);
        }

        return $resumePaginator;
    }

    /**
     * {@inheritDoc}
     */
    public function publish(Resume $resume, bool $force = FALSE): bool {
        // Store the id of the resume.
        $resumeId = $resume->getId();

        // If the resume is already published, throw an exception.
        if ($resume->getStatus() === ResumeStatusEnum::ONLINE) {
            throw new NSResumeException\ResumePublicationException("flashes.error.resume.already-published", "Resume No. $resumeId already published!");
        }

        // Check if the resume is not anonymous and the user has no profile.
        $author = $resume->getAuthor();
        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['user' => $author]);
        if (!$resume->getAnonymous() && null === $profile) {
            throw new NSResumeException\ResumePublicationException("flashes.error.resume.no-profile", "No profile set for non anonymous resume!");
        }

        // Check the status of the resume.
        if (FALSE === $force && $resume->getStatus() === ResumeStatusEnum::OFFLINE_INCOMPLETE) {
            throw new NSResumeException\ResumeIncompleteException("flashes.error.resume.incomplete", "Resume No. $resumeId is incomplete!");
        }

        // For all other cases, we update the status.
        $resume->setStatus(ResumeStatusEnum::ONLINE);
        $this->entityManager->flush($resume);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function removeWithDependencies(Resume $resume): bool {
        $this->entityManager->beginTransaction();
        $resumeId = $resume->getId();
        try {
            // Try to remove the resume skills linked.
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                // Remove the entity from the collection.
                $resume->removeResumeSkill($resumeSkill);
                // Remove the entity.
                $this->entityManager->remove($resumeSkill);
            }

            // Try to remove the educations linked.
            foreach ($resume->getEducations() as $education) {
                // Remove the entity from the collection.
                $resume->removeEducation($education);
                // Remove the entity.
                $this->entityManager->remove($education);
            }

            foreach ($resume->getExperiences() as $experience) {
                // Remove the entity from the collection.
                $resume->removeExperience($experience);
                // Remove the entity.
                $this->entityManager->remove($experience);
            }

            // Remove the resume.
            $this->entityManager->remove($resume);
            $this->entityManager->flush();

            // Commit the transaction.
            $this->entityManager->commit();
            return true;
        } catch (\Exception | \Error $exc) {
            // Rollback the transaction and throw a fonctional exception.
            $this->entityManager->rollback();

            throw new FunctionalException("flashes.error.resume.delete", "An error occured while the removal of resume No. $resumeId", 0, $exc);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unpublish(Resume $resume): void {
        // Init the dependencies counter.
        $countDependencies = 0;

        // Add educations count
        $countDependencies += $resume->getEducations()->count();
        // Add experiences count
        $countDependencies += $resume->getExperiences()->count();
        // Add resume skills count
        $countDependencies += $resume->getResumeSkills()->count();

        // By default we switch the status to offline / incomplete.
        $resume->setStatus(ResumeStatusEnum::OFFLINE_INCOMPLETE);

        // If the resume has at least one of each entities, we switch the status to "offline / to publish".
        if ($countDependencies >= 3) {
            $resume->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED);
        }

        // Commit the update query.
        $this->entityManager->flush();
    }

}
