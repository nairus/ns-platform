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
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private static $dispatcher;

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        static::$dispatcher = static::$container->get("event_dispatcher");
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $author = static::$em->getRepository(User::class)->findOneBy(["username" => "user"]);
        $resumes = static::$em->getRepository(NSResumeEntity\Resume::class)->findBy(["author" => $author]);

        foreach ($resumes as /* @var $resume NSResumeEntity\Resume */ $resume) {
            // Clean the datas properly .
            foreach ($resume->getEducations() as $education) {
                $resume->removeEducation($education);
                static::$em->remove($education);
            }
            foreach ($resume->getExperiences() as $experience) {
                $resume->removeExperience($experience);
                static::$em->remove($experience);
            }
            foreach ($resume->getResumeSkills() as $resumeSkill) {
                $resume->removeResumeSkill($resumeSkill);
                static::$em->remove($resumeSkill);
            }
            static::$em->remove($resume);
        }
        static::$em->flush();

        $loadSkill = new LoadSkill();
        $loadSkill->remove(static::$em);
        $loadSkillLevel = new LoadSkillLevel();
        $loadSkillLevel->remove(static::$em);

        parent::tearDown();

        unset($this->resume);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        // Create the new resume.
        $author = static::$em->getRepository(User::class)->findOneBy(["username" => "user"]);
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
        $resumeStatusEventCase1 = new ResumeStatusEvent($this->resume);
        static::$dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase1);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $this->resume->getStatus(), "1. The status has to remain unchanged.");

        // Case 2: Add education entity.
        $this->addEducation($this->resume);
        $resumeStatusEventCase2 = new ResumeStatusEvent($this->resume);
        static::$dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase2);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $this->resume->getStatus(), "2.1 The status has to remain unchanged.");
        $this->assertCount(1, $this->resume->getEducations(), "2.2 The resume has to contain one [Education] entity.");

        // Case 3: Add experience entity.
        $this->addExperience($this->resume, 1, false);
        $resumeStatusEventCase3 = new ResumeStatusEvent($this->resume);
        static::$dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase3);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $this->resume->getStatus(), "3.1 The status has to remain unchanged.");
        $this->assertCount(1, $this->resume->getExperiences(), "3.2 The resume has to contain one [Experience] entity.");

        // Case 4: Add resume skill entity.
        $this->addResumeSkill($this->resume);
        $resumeStatusEventCase4 = new ResumeStatusEvent($this->resume);
        static::$dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase4);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $this->resume->getStatus(), "4.1 The status has to be changed.");
        $this->assertCount(1, $this->resume->getResumeSkills(), "4.2 The resume has to contain one [ResumeSkill] entity.");

        // Case 5: Add another experience entity.
        $this->addExperience($this->resume, 2, true);
        $resumeStatusEventCase5 = new ResumeStatusEvent($this->resume);
        static::$dispatcher->dispatch(NSResumeEvents::UPDATE_STATUS, $resumeStatusEventCase5);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $this->resume->getStatus(), "5.1 The status has not to be changed.");
        $this->assertCount(2, $this->resume->getExperiences(), "5.2 The resume has to contain two [Experience] entities.");
    }

    /**
     * Test the <code>onDeleteStatus</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Listener\ResumeStatusListener::onDeleteStatus
     *
     * @return void
     */
    public function testOnDeleteStatusOffLineToPublishWithNoResumeSkill(): void {
        // Case 1: Update resume status if there is no resume skill.
        // Prepare resume datas
        $resume = $this->newResume();
        $this->addEducation($resume);
        $this->addExperience($resume, 1);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $resume->getStatus(), "1. The status has to be correct.");

        $resumeStatusEvent = new ResumeStatusEvent($resume);
        static::$dispatcher->dispatch(NSResumeEvents::DELETE_STATUS, $resumeStatusEvent);
        static::$em->refresh($resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $resume->getStatus(), "2. The status has to be changed.");
    }

    /**
     * Test the <code>onDeleteStatus</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Listener\ResumeStatusListener::onDeleteStatus
     *
     * @return void
     */
    public function testOnDeleteStatusOffLineToPublishWithNoExperience(): void {
        // Case 2: Update resume status if there is no experience.
        // Prepare resume datas
        $resume = $this->newResume();
        $this->addEducation($resume);
        $this->addResumeSkill($resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $resume->getStatus(), "1. The status has to be correct.");

        $resumeStatusEvent = new ResumeStatusEvent($resume);
        static::$dispatcher->dispatch(NSResumeEvents::DELETE_STATUS, $resumeStatusEvent);
        static::$em->refresh($resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $resume->getStatus(), "2. The status has to be changed.");
    }

    /**
     * Test the <code>onDeleteStatus</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Listener\ResumeStatusListener::onDeleteStatus
     *
     * @return void
     */
    public function testOnDeleteStatusOffLineToPublishWithNoEducation(): void {
        // Case 3: Update resume status if there is no education.
        // Prepare resume datas
        $resume = $this->newResume();
        $this->addExperience($resume, 1);
        $this->addResumeSkill($resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_TO_PUBLISHED, $resume->getStatus(), "1. The status has to be correct.");

        $resumeStatusEvent = new ResumeStatusEvent($resume);
        static::$dispatcher->dispatch(NSResumeEvents::DELETE_STATUS, $resumeStatusEvent);
        static::$em->refresh($resume);
        $this->assertEquals(ResumeStatusEnum::OFFLINE_INCOMPLETE, $resume->getStatus(), "2. The status has to be changed.");
    }

    /**
     * Test the <code>onDeleteStatus</code> method.
     *
     * @covers Com\Nairus\ResumeBundle\Listener\ResumeStatusListener::onDeleteStatus
     *
     * @return void
     */
    public function testOnDeleteStatusOnline(): void {
        // Case 4: Update resume status if there is no datas but online status.
        // Prepare resume datas
        $this->resume->setStatus(ResumeStatusEnum::ONLINE);
        static::$em->flush($this->resume);
        $this->assertEquals(ResumeStatusEnum::ONLINE, $this->resume->getStatus(), "1. The status has to be correct.");

        $resumeStatusEvent = new ResumeStatusEvent($this->resume);
        static::$dispatcher->dispatch(NSResumeEvents::DELETE_STATUS, $resumeStatusEvent);
        static::$em->refresh($this->resume);
        $this->assertEquals(ResumeStatusEnum::ONLINE, $this->resume->getStatus(), "2. The status has not to be changed.");
    }

    /**
     * Create a new Resume instance.
     *
     * @return \Com\Nairus\ResumeBundle\Entity\Resume
     */
    private function newResume(): NSResumeEntity\Resume {
        $author = static::$em->getRepository(User::class)->findOneBy(["username" => "user"]);
        $resume = new NSResumeEntity\Resume();
        $resume->setAnonymous(true)
                ->setIp("127.0.0.1")
                ->setStatus(ResumeStatusEnum::OFFLINE_TO_PUBLISHED)
                ->setAuthor($author)
                ->setCurrentLocale("fr")
                ->setTitle("Mon CV");
        static::$em->persist($resume);
        static::$em->flush($resume);
        return $resume;
    }

    /**
     * Add an education.
     *
     * @return void
     */
    private function addEducation(NSResumeEntity\Resume $resume): void {
        static::$em->refresh($resume);
        $education = new NSResumeEntity\Education();
        $education->setDiploma("Diplôme")
                ->setEndYear(2017)
                ->setInstitution("Institution")
                ->setResume($resume)
                ->setStartYear(2016);
        static::$em->persist($education);
        static::$em->flush($education);
    }

    private function addExperience(NSResumeEntity\Resume $resume, int $number, bool $currentJob = false): void {
        static::$em->refresh($resume);
        $experience = new NSResumeEntity\Experience();
        $experience->setCompany("Company" . $number)
                ->setCurrentJob($currentJob)
                ->setLocation("Location" . $number)
                ->setResume($resume)
                ->setStartMonth(10 - $number)
                ->setStartYear(2018 - $number);

        if (!$currentJob) {
            $experience->setEndMonth(10 + $number)
                    ->setEndYear(2018 + $number);
        }
        static::$em->persist($experience);
        static::$em->flush($experience);
    }

    private function addResumeSkill(NSResumeEntity\Resume $resume, int $rank = 1) {
        static::$em->refresh($resume);
        $skill = static::$em->getRepository(NSResumeEntity\Skill::class)->findAll()[$rank - 1];
        $skillLevel = static::$em->getRepository(NSResumeEntity\SkillLevel::class)->findAll()[$rank - 1];
        $resumeSkill = new NSResumeEntity\ResumeSkill();
        $resumeSkill->setRank($rank)
                ->setResume($resume)
                ->setSkill($skill)
                ->setSkillLevel($skillLevel);
        static::$em->persist($resumeSkill);
        static::$em->flush($resumeSkill);
    }

}
