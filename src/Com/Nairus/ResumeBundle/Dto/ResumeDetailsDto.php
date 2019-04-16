<?php

namespace Com\Nairus\ResumeBundle\Dto;

use Com\Nairus\ResumeBundle\Collection as NSResumeCollection;
use Com\Nairus\ResumeBundle\Entity\Profile;

/**
 * Details for a resume entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeDetailsDto {

    /**
     * User profile if the resume is not anonymous.
     *
     * @var Profile
     */
    private $profile;

    /**
     * Collection of the education entities ordered.
     *
     * @var NSResumeCollection\EducationCollection
     */
    private $educations;

    /**
     * Collection of the experience entities ordered.
     *
     * @var NSResumeCollection\ExperienceCollection
     */
    private $experiences;

    /**
     * Collection of the the resumeSkill entities ordered.
     *
     * @var NSResumeCollection\ResumeSkillCollection
     */
    private $resumeSkills;

    /**
     * The constructor.
     */
    public function __construct() {
        $this->educations = new NSResumeCollection\EducationCollection();
        $this->experiences = new NSResumeCollection\ExperienceCollection();
        $this->resumeSkills = new NSResumeCollection\ResumeSkillCollection();
    }

    /**
     * Return the profile of the resume.
     *
     * @return Profile|null
     */
    public function getProfile(): ?Profile {
        return $this->profile;
    }

    /**
     * Return the educations's collection.
     *
     * @return \Com\Nairus\ResumeBundle\Collection\EducationCollection
     */
    public function getEducations(): NSResumeCollection\EducationCollection {
        return $this->educations;
    }

    /**
     * Return the experiences's collection.
     *
     * @return \Com\Nairus\ResumeBundle\Collection\ExperienceCollection
     */
    public function getExperiences(): NSResumeCollection\ExperienceCollection {
        return $this->experiences;
    }

    /**
     * Return the resumeSkills's collection.
     *
     * @return \Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection
     */
    public function getResumeSkills(): NSResumeCollection\ResumeSkillCollection {
        return $this->resumeSkills;
    }

    /**
     * Define the resume's profile.
     *
     * @param Profile $profile The resume's profile to set.
     *
     * @return ResumeDetailsDto
     */
    public function setProfile(Profile $profile): ResumeDetailsDto {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Define the education's collection.
     *
     * @param \Com\Nairus\ResumeBundle\Collection\EducationCollection $educations The education's collection to set.
     *
     * @return \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto
     */
    public function setEducations(NSResumeCollection\EducationCollection $educations): ResumeDetailsDto {
        $this->educations = $educations;
        return $this;
    }

    /**
     * Define the experience's collection.
     *
     * @param \Com\Nairus\ResumeBundle\Collection\ExperienceCollection $experiences The experience's collection to set.
     *
     * @return \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto
     */
    public function setExperiences(NSResumeCollection\ExperienceCollection $experiences): ResumeDetailsDto {
        $this->experiences = $experiences;
        return $this;
    }

    /**
     * Define the resumeSkill's collection.
     *
     * @param \Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection $resumeSkills The resumeSkill's collection to set.
     *
     * @return \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto
     */
    public function setResumeSkills(NSResumeCollection\ResumeSkillCollection $resumeSkills): ResumeDetailsDto {
        $this->resumeSkills = $resumeSkills;
        return $this;
    }

}
