<?php

namespace Com\Nairus\ResumeBundle\Helper;

use PHPUnit\Framework\TestCase;

/**
 * Test of ResumeHelper.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeHelperTest extends TestCase {

    /**
     * @var ResumeHelper
     */
    private $resumeHelper;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->resumeHelper = new ResumeHelper();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        unset($this->resumeHelper);
    }

    /**
     * Test the <code>isComplete</code>.
     *
     * @return void
     */
    public function testIsComplete(): void {
        $resume = new \Com\Nairus\ResumeBundle\Entity\Resume();
        $resume->setAnonymous(true);
        $dto = new \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto($resume);
        $dto->setEducations(new \Com\Nairus\ResumeBundle\Collection\EducationCollection([new \Com\Nairus\ResumeBundle\Entity\Education()]))
                ->setExperiences(new \Com\Nairus\ResumeBundle\Collection\ExperienceCollection([new \Com\Nairus\ResumeBundle\Entity\Experience()]))
                ->setResumeSkills(new \Com\Nairus\ResumeBundle\Collection\ResumeSkillCollection([new \Com\Nairus\ResumeBundle\Entity\ResumeSkill()]));
        $this->assertTrue($this->resumeHelper->isComplete($dto));
    }

    /**
     * Test the <code>isComplete</code> with no profile.
     *
     * @return void
     */
    public function testIsCompleteWithNoProfile(): void {
        $resume = new \Com\Nairus\ResumeBundle\Entity\Resume();
        $resume->setAnonymous(false);
        $this->assertFalse($this->resumeHelper->isComplete(new \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto($resume)));
    }

    /**
     * Test the <code>isComplete</code> with no education.
     *
     * @return void
     */
    public function testIsCompleteWithNoEducation(): void {
        $resume = new \Com\Nairus\ResumeBundle\Entity\Resume();
        $resume->setAnonymous(false);
        $dto = new \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto($resume);
        $dto->setProfile(new \Com\Nairus\ResumeBundle\Entity\Profile());
        $this->assertFalse($this->resumeHelper->isComplete($dto));
    }

    /**
     * Test the <code>isComplete</code> with no experience.
     *
     * @return void
     */
    public function testIsCompleteWithNoExperience(): void {
        $resume = new \Com\Nairus\ResumeBundle\Entity\Resume();
        $resume->setAnonymous(false);
        $dto = new \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto($resume);
        $dto->setProfile(new \Com\Nairus\ResumeBundle\Entity\Profile())
                ->setEducations(new \Com\Nairus\ResumeBundle\Collection\EducationCollection([new \Com\Nairus\ResumeBundle\Entity\Education()]));
        $this->assertFalse($this->resumeHelper->isComplete($dto));
    }

    /**
     * Test the <code>isComplete</code> with no resumeSkill.
     *
     * @return void
     */
    public function testIsCompleteWithNoResumeSkill(): void {
        $resume = new \Com\Nairus\ResumeBundle\Entity\Resume();
        $resume->setAnonymous(false);
        $dto = new \Com\Nairus\ResumeBundle\Dto\ResumeDetailsDto($resume);
        $dto->setProfile(new \Com\Nairus\ResumeBundle\Entity\Profile())
                ->setEducations(new \Com\Nairus\ResumeBundle\Collection\EducationCollection([new \Com\Nairus\ResumeBundle\Entity\Education()]))
                ->setExperiences(new \Com\Nairus\ResumeBundle\Collection\ExperienceCollection([new \Com\Nairus\ResumeBundle\Entity\Experience()]));
        $this->assertFalse($this->resumeHelper->isComplete($dto));
    }

}
