<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\Constants\ExceptionCodeConstants;
use Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto;
use Com\Nairus\ResumeBundle\Dto\ResumePaginatorDto;
use Com\Nairus\ResumeBundle\Exception as NSResumeException;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity as NSResumeEntity;
use Com\Nairus\ResumeBundle\Repository as NSResumeRepository;
use Com\Nairus\ResumeBundle\Service\ResumeServiceInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Service of Resume.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeService implements ResumeServiceInterface {

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var NSResumeRepository\ResumeRepository
     */
    private $resumeRepository;

    /**
     * @var NSResumeRepository\ProfileRepository
     */
    private $profileRepository;

    /**
     * @var NSResumeRepository\EducationRepository
     */
    private $educationRepository;

    /**
     * @var NSResumeRepository\ExperienceRepository
     */
    private $experienceRepository;

    /**
     * @var NSResumeRepository\ResumeSkillRepository
     */
    private $resumeSkillRepository;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager The current entity manager.
     */
    public function __construct(ObjectManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->resumeRepository = $entityManager->getRepository(NSResumeEntity\Resume::class);
        $this->profileRepository = $entityManager->getRepository(NSResumeEntity\Profile::class);
        $this->educationRepository = $entityManager->getRepository(NSResumeEntity\Education::class);
        $this->experienceRepository = $entityManager->getRepository(NSResumeEntity\Experience::class);
        $this->resumeSkillRepository = $entityManager->getRepository(NSResumeEntity\ResumeSkill::class);
    }

    /**
     * {@inheritDoc}
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage, string $locale): ResumePaginatorDto {
        if ($page < 1) {
            throw new NSResumeException\ResumeListException($page, "Wrong page", ExceptionCodeConstants::WRONG_PAGE);
        }

        $resumePaginator = $this->resumeRepository->findAllOnlineForPage($page, $nbPerPage, $locale);
        $entities = $resumePaginator->getIterator()->getArrayCopy();
        if ($page > 1 && 0 === count($entities)) {
            throw new NSResumeException\ResumeListException($page, "Page not found", ExceptionCodeConstants::PAGE_NOT_FOUND);
        }

        $nbPages = ceil($resumePaginator->count() / $nbPerPage);
        $dto = new ResumePaginatorDto();
        $dto->setCurrentPage($page)
                ->setPages($nbPages)
                ->setEntities($entities);
        return $dto;
    }

    /**
     * {@inheritDoc}
     */
    public function publish(NSResumeEntity\Resume $resume, bool $force = FALSE): bool {
        // Store the id of the resume.
        $resumeId = $resume->getId();

        // If the resume is already published, throw an exception.
        if ($resume->getStatus() === ResumeStatusEnum::ONLINE) {
            throw new NSResumeException\ResumePublicationException("flashes.error.resume.already-published", "Resume No. $resumeId already published!");
        }

        // Check if the resume is not anonymous and the user has no profile.
        $author = $resume->getAuthor();
        $profile = $this->profileRepository->findOneBy(['user' => $author]);
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
    public function removeWithDependencies(NSResumeEntity\Resume $resume): bool {
        $this->entityManager->beginTransaction();
        $resumeId = $resume->getId();
        try {
            // Try to remove the resume skills linked.
            foreach ($this->resumeSkillRepository->findBy(['resume' => $resume]) as $resumeSkill) {
                // Remove the entity from the collection.
                $resume->removeResumeSkill($resumeSkill);
                // Remove the entity.
                $this->entityManager->remove($resumeSkill);
            }

            // Try to remove the educations linked.
            foreach ($this->educationRepository->findBy(['resume' => $resume]) as $education) {
                // Remove the entity from the collection.
                $resume->removeEducation($education);
                // Remove the entity.
                $this->entityManager->remove($education);
            }

            foreach ($this->experienceRepository->findBy(['resume' => $resume]) as $experience) {
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
    public function unpublish(NSResumeEntity\Resume $resume): void {
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

    /**
     * {@inheritDoc}
     */
    public function getDetailsForResumeId(int $resumeId, string $locale): ResumeDetailsDto {
        // Get the resume with the id.
        $resume = $this->resumeRepository->findWithTranslationAndAuthor($resumeId, $locale);
        if (null === $resume) {
            throw new \Doctrine\ORM\EntityNotFoundException("Resume not found for the id: `$resumeId` and the locale `$locale`.");
        }

        $resumeDetailsDto = new ResumeDetailsDto($resume);

        // Get the profile with avatar if not anonymous.
        if (!$resumeDetailsDto->isAnonymous()) {
            $resumeDetailsDto->setProfile($this->profileRepository->getWithAvatarForUser($resume->getAuthor()));
        }

        // Get the resume's details
        $resumeDetailsDto->setEducations($this->educationRepository->findOrderedForResumeId($resume->getId(), $locale))
                ->setExperiences($this->experienceRepository->findOrderedForResumeId($resume->getId(), $locale))
                ->setResumeSkills($this->resumeSkillRepository->findOrderedByRank($resume->getId(), $locale));

        return $resumeDetailsDto;
    }

}
