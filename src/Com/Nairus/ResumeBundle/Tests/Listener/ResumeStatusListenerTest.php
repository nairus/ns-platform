<?php

namespace Com\Nairus\ResumeBundle\Listener;

use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Entity as NSResumeEntity;
use Com\Nairus\ResumeBundle\Event\NSResumeEvents;
use Com\Nairus\ResumeBundle\Event\ResumeStatusEvent;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;
use Com\Nairus\UserBundle\Entity\User;

/**
 * Test of ResumeStatusListener.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeStatusListenerTest extends AbstractKernelTestCase {

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;

    /**
     * @var NSResumeEntity\Resume
     */
    private $resume;

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        // Clean the datas properly .
        foreach ($this->resume->getEducations() as $education) {
            $this->resume->removeEducation($education);
            static::$em->remove($education);
        }
        foreach ($this->resume->getExperiences() as $experience) {
            $this->resume->removeExperience($experience);
            static::$em->remove($experience);
        }
        foreach ($this->resume->getResumeSkills() as $resumeSkill) {
            $this->resume->removeResumeSkill($resumeSkill);
            static::$em->remove($resumeSkill);
        }
        static::$em->remove($this->resume);
        static::$em->flush();

        unset($this->resume);
        parent::tearDown();
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        parent::setUp();
        // Get the author of the resume.
        /* @var $author UserInterface */
        $author = static::$em->getRepository(User::class)->findOneBy(["username" => "author"]);

        // Create the new resume.
        $this->resume = new NSResumeEntity\Resume();
        $this->resume->setAnonymous(true)
                ->setIp("127.0.0.1")
                ->setStatus(ResumeStatusEnum::OFFLINE_INCOMPLETE)
                ->setAuthor($author);
        $this->loadDatas(static::$em, [new LoadSkill(), new LoadSkillLevel(), $this->resume]);
    }

    /**
     * Test the <code>getSubscribedEvents</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Listener\ResumeStatusListener::getSubscribedEvents
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void {
        $subscribedEvents = ResumeStatusListener::getSubscribedEvents();
        $this->assertContains(NSResumeEvents::UPDATE_STATUS, $subscribedEvents, "1. The array of events has to contain [UPDATE_STATUS] event.");
    }

    /**
     * Test constructor.
     *
     * @return void
     */
    public function testConstructor(): void {
        $resumeStatusListener = new ResumeStatusListener(static::$em);
        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\EventSubscriberInterface::class, $resumeStatusListener,
                "1. The listener has to implement [EventSubscriberInterface] interface");
    }

    /**
     * Test the <code>onUpdateStatus</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Listener\ResumeStatusListener::onUpdateStatus
     *
     * @return void
     */
    public function testOnUpdateStatus(): void {
        // Case 1: Create a new resume.
        /* @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = static::$container->get("event_dispatcher");
        $resumeStatusEventCase1 = new ResumeStatusEvent($this->resume);
        $dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase1);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $this->resume->getStatus(), "1. The status has to remain unchanged.");

        // Case 2: Add education entity.
        $education = new NSResumeEntity\Education();
        $education->setDiploma("Diplôme")
                ->setEndYear(2017)
                ->setInstitution("Institution")
                ->setResume($this->resume)
                ->setStartYear(2016);
        static::$em->persist($education);
        static::$em->flush($education);
        $resumeStatusEventCase2 = new ResumeStatusEvent($this->resume);
        $dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase2);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $this->resume->getStatus(), "2.1 The status has to remain unchanged.");
        $this->assertCount(1, $this->resume->getEducations(), "2.2 The resume has to contain one [Education] entity.");

        // Case 3: Add experience entity.
        $experience = new NSResumeEntity\Experience();
        $experience->setCompany("Company")
                ->setCurrentJob(true)
                ->setLocation("Location")
                ->setResume($this->resume)
                ->setStartMonth(10)
                ->setStartYear(2018);
        static::$em->persist($experience);
        static::$em->flush($experience);
        $resumeStatusEventCase3 = new ResumeStatusEvent($this->resume);
        $dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase3);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $this->resume->getStatus(), "3.1 The status has to remain unchanged.");
        $this->assertCount(1, $this->resume->getExperiences(), "3.2 The resume has to contain one [Experience] entity.");

        // Case 4: Add resume skill entity.
        $skill = static::$em->getRepository(NSResumeEntity\Skill::class)->findAll()[0];
        $skillLevel = static::$em->getRepository(NSResumeEntity\SkillLevel::class)->findAll()[0];
        $resumeSkill = new NSResumeEntity\ResumeSkill();
        $resumeSkill->setRank(1)
                ->setResume($this->resume)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel);
        static::$em->persist($resumeSkill);
        static::$em->flush($resumeSkill);
        $resumeStatusEventCase4 = new ResumeStatusEvent($this->resume);
        $dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase4);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $this->resume->getStatus(), "4.1 The status has to be changed.");
        $this->assertCount(1, $this->resume->getResumeSkills(), "4.2 The resume has to contain one [ResumeSkill] entity.");

        // Case 5: Add another experience entity.
        $experience2 = new NSResumeEntity\Experience();
        $experience2->setCompany("Company 2")
                ->setCurrentJob(false)
                ->setLocation("Location 2")
                ->setResume($this->resume)
                ->setStartMonth(1)
                ->setStartYear(2016)
                ->setEndMonth(9)
                ->setEndYear(2018);
        static::$em->persist($experience2);
        static::$em->flush($experience2);
        $resumeStatusEventCase5 = new ResumeStatusEvent($this->resume);
        $dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase5);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $this->resume->getStatus(), "5.1 The status has not to be changed.");
        $this->assertCount(2, $this->resume->getExperiences(), "5.2 The resume has to contain two [Experience] entities.");
    }

}
