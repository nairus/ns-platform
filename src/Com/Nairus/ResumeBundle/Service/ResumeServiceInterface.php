<?php

namespace Com\Nairus\ResumeBundle\Service;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto;
use Com\Nairus\ResumeBundle\Dto\ResumePaginatorDto;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Exception\ResumePublicationException;

/**
 * Interface of the resume service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface ResumeServiceInterface {

    /**
     * Return a collection of ResumeListDto
     *
     * @param int    $page      The current page.
     * @param int    $nbPerPage The number of elements per page.
     * @param string $locale    The current locale.
     *
     * @return ResumePaginatorDto
     *
     * @throws \Com\Nairus\ResumeBundle\Exception\ResumeListException When an error occurs.
     */
    public function findAllOnlineForPage(int $page, int $nbPerPage, string $locale): ResumePaginatorDto;

    /**
     * Publish a resume.
     *
     * @param Resume $resume The resume to publish.
     * @param bool   $force  Force the publication of the resume (if incomplete).
     *
     * @return bool <code>true</true> if publication succeed.
     *
     * @throws ResumePublicationException when publication fails.
     */
    public function publish(Resume $resume, bool $force = FALSE): bool;

    /**
     * Remove a resume with his dependencies.
     *
     * @param Resume $resume The resume to remove.
     *
     * @return bool <code>TRUE</code> if the removal had succeed.
     *
     * @throws FunctionalException if an error occured.
     */
    public function removeWithDependencies(Resume $resume): bool;

    /**
     * Unpublish a resume.
     *
     * @param Resume $resume The resume to unpublish.
     *
     * @return void
     */
    public function unpublish(Resume $resume): void;

    /**
     * Return the details of the resume.
     *
     * @param Resume $resume The current resume.
     * @param string $locale The current locale.
     *
     * @return ResumeDetailsDto
     */
    public function getDetailsForResume(Resume $resume, string $locale): ResumeDetailsDto;
}
