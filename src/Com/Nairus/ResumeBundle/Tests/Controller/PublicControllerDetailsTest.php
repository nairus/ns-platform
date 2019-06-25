<?php

namespace Com\Nairus\ResumeBundle\Tests\Controller;

use Com\Nairus\CoreBundle\Tests\BaseWebTestCase;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadResumeDetails;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkill;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadSkillLevel;

/**
 * Test of Public details action.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PublicControllerDetailsTest extends BaseWebTestCase {

    /**
     * Project directory.
     *
     * @var string
     */
    protected static $projectDirectory;

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\FileCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        parent::setUp();

        // Create the target image directory
        static::$projectDirectory = $this->getClient()->getContainer()->getParameter('kernel.project_dir');
        $dirname = static::$projectDirectory . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "image_manager";
        if (!\is_dir($dirname)) {
            \mkdir($dirname);
        }
        $this->loadDatas($this->getEntityManager(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeDetails()]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        $this->cleanDatas($this->getClient()->getContainer(), [new LoadSkill(), new LoadSkillLevel(), new LoadResumeDetails()]);

        // remove the target image directory
        $dirname = static::$projectDirectory . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "image_manager";
        $this->cleanAndRemoveFolder($dirname);

        parent::tearDown();
    }

    /**
     * Test the `detailsAction`.
     *
     * @return void
     */
    public function testDetailsActionFr(): void {
        $crawler = $this->getClient()->request("GET", "/resume/");

        // Select the first tile link with profile.
        $firstLink = $crawler->filter(".resume-tile")->eq(0)->filter(".card-link")->link();
        $crawler = $this->getClient()->click($firstLink);

        $this->assertRegExp("~^/resume/[a-z0-9-]+\.html$~", $this->getClient()->getRequest()->getRequestUri(), "1.1. The request uri expected is not ok.");
        $this->assertRegExp("~^Détails du CV -.+- n°[0-9]+~", $crawler->filter("html > head > title")->text(), "1.2. The page title expected is not ok.");
        $this->assertEquals(3, $crawler->filter("#details-container > section")->count(), "1.3. Three sections are expected.");
        $this->assertEquals("Mes expériences professionnelles", $crawler->filter("#details-container > section")->eq(0)->filter("h2")->text(),
                "1.4. The first section label expected is not ok.");
        $this->assertEquals("Mes diplômes", $crawler->filter("#details-container > section")->eq(1)->filter("h2")->text(),
                "1.5. The second section label expected is not ok.");
        $this->assertEquals("Mes compétences", $crawler->filter("#details-container > section")->eq(2)->filter("h2")->text(),
                "1.6. The third section label expected is not ok.");
        $this->assertNotNull($crawler->filter("#profile"), "1.7 The profile bloc has to be displayed");

        // get the avatar of the profile.
        $user = $this->getEntityManager()->getRepository(Com\Nairus\UserBundle\Entity\User::class)
                ->findOneByUsername(Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase::ADMIN);
        /* @var $profile Com\Nairus\ResumeBundle\Entity\Profile */
        $profile = $this->getEntityManager()->getRepository(Com\Nairus\ResumeBundle\Entity\Profile::class)->findOneByUser($user);
        $avatar = $profile->getAvatar();
        $expectedImageSrc = $avatar->getRelativePath() . 'src-' . $avatar->getId() . '.' . $avatar->getExtension();
        $imageSrc = $crawler->filter("#profile > img")->getNode(0)->getAttribute("src");
        $this->assertEquals($expectedImageSrc, $imageSrc, "1.8 The image src path expected is not ok.");

        // Select the first tile link with no profile.
        $crawler = $this->getClient()->back();
        $secondLink = $crawler->filter(".resume-tile")->eq(1)->filter(".card-link")->link();
        $crawler = $this->getClient()->click($secondLink);
        $this->assertNotNull($crawler->filter("#profile"), "2.1 The profile bloc has to be displayed");
    }

    /**
     * Test the `detailsAction`.
     *
     * @return void
     */
    public function testDetailsActionEn(): void {
        $crawler = $this->getClient()->request("GET", "/en/resume/");

        // Select the first tile link.
        $firstLink = $crawler->filter(".resume-tile")->eq(0)->filter(".card-link")->link();
        $crawler = $this->getClient()->click($firstLink);

        $this->assertRegExp("~^/en/resume/[a-z0-9-]+\.html$~", $this->getClient()->getRequest()->getRequestUri(), "1. The request uri expected is not ok.");
        $this->assertRegExp("~^Resume's details - .+ - No [0-9]+~", $crawler->filter("html > head > title")->text(), "1.2. The page title expected is not ok.");
        $this->assertEquals(3, $crawler->filter("#details-container > section")->count(), "1.3. Three sections are expected.");
        $this->assertEquals("My professional experiences", $crawler->filter("#details-container > section")->eq(0)->filter("h2")->text(),
                "1.4. The first section label expected is not ok.");
        $this->assertEquals("My qualifications", $crawler->filter("#details-container > section")->eq(1)->filter("h2")->text(),
                "1.5. The second section label expected is not ok.");
        $this->assertEquals("My skills", $crawler->filter("#details-container > section")->eq(2)->filter("h2")->text(),
                "1.6. The third section label expected is not ok.");
    }

    /**
     * Test the `detailsAction` with no resume.
     *
     * @return void
     */
    public function testDetailsActionWithNoResume(): void {
        $this->getClient()->request("GET", "/resume/not-exists-9999999.html");
        $this->assertEquals(410, $this->getClient()->getResponse()->getStatusCode(), "The status code expected is not ok.");
    }

    /**
     * Test the `detailsAction` with an offline resume.
     *
     * @return void
     */
    public function testDetailsActionWithOfflineResume(): void {
        $user = $this->getEntityManager()->getRepository(Com\Nairus\UserBundle\Entity\User::class)->findOneByUsername(\Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase::ADMIN);
        $criteria = ['status' => Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum::OFFLINE_INCOMPLETE, 'author' => $user];
        /* @var $resumeOffline \Com\Nairus\ResumeBundle\Entity\Resume */
        $resumeOffline = $this->getEntityManager()->getRepository(\Com\Nairus\ResumeBundle\Entity\Resume::class)->findOneBy($criteria);

        $resumeId = $resumeOffline->getId();
        $slug = $resumeOffline->getSlug();
        $this->getClient()->request("GET", "/resume/$slug-$resumeId.html");
        $this->assertEquals(410, $this->getClient()->getResponse()->getStatusCode(), "The status code expected is not ok.");
    }

    /**
     * Test the `detailsAction` with an incomplete resume.
     *
     * @return void
     */
    public function testDetailsActionWithIncompleteResume(): void {
        $crawler = $this->getClient()->request("GET", "/resume/");

        // Select the first tile link.
        $link = $crawler->filter(".resume-tile")->eq(1)->filter(".card-link")->link();
        // Go to the fr details
        $crawler = $this->getClient()->click($link);
        $this->assertRegExp("~^/resume/[a-z0-9-]+\.html$~", $this->getClient()->getRequest()->getRequestUri(), "1. The request uri expected is not ok.");

        // Click on the english flag
        $this->getClient()->click($crawler->filter("#language-dropdown-menu")->selectLink("Anglais")->link());
        $this->assertEquals(410, $this->getClient()->getResponse()->getStatusCode(), "2. The status code expected is not ok.");
    }

    /**
     * Test the `detailsAction` with an incomplete resume.
     *
     * @return void
     */
    public function testDetailsActionWithBadSlug(): void {
        $user = $this->getEntityManager()->getRepository(Com\Nairus\UserBundle\Entity\User::class)->findOneByUsername(\Com\Nairus\UserBundle\Tests\AbstractUserWebTestCase::ADMIN);
        $criteria = ['status' => Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum::ONLINE, 'author' => $user];
        /* @var $resume \Com\Nairus\ResumeBundle\Entity\Resume */
        $resume = $this->getEntityManager()->getRepository(\Com\Nairus\ResumeBundle\Entity\Resume::class)->findOneBy($criteria);

        $resumeId = $resume->getId();
        $goodSlug = $resume->getSlug();
        $this->getClient()->followRedirects(false);
        $this->getClient()->request("GET", "/resume/bad-slug-$resumeId.html");
        $this->assertEquals(301, $this->getClient()->getResponse()->getStatusCode(), "1. The status code expected is not ok.");
        $this->getClient()->followRedirect();
        $this->assertEquals("/resume/$goodSlug-$resumeId.html", $this->getClient()->getRequest()->getRequestUri(), "2. The redirect uri expected is not ok.");
    }

}
