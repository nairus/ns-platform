<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Repository\NewsRepository;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadNewsPublished;

/**
 * Test of the News service.
 *
 * @author nairus
 */
class NewsServiceTest extends AbstractKernelTestCase {

    /**
     * @var NewsService
     */
    private $object;

    /**
     * @var LoadNewsPublished
     */
    private static $loadNewsPublished;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$loadNewsPublished = new LoadNewsPublished();
        static::$loadNewsPublished->load(static::$em);
    }

    public static function tearDownAfterClass() {
        static::$loadNewsPublished->remove(static::$em);
    }

    protected function setUp() {
        $this->object = new NewsService(static::$em);
        $this->object->setAvailableLocales(["fr", "en", "ru"]);
    }

    protected function tearDown() {
        unset($this->object);
    }

    /**
     * Test the implementation of NewsServiceInterface.
     */
    public function testImplementation() {
        $this->assertInstanceOf(NewsServiceInterface::class, $this->object, "1. The service has to be of type [NewsServiceInterface].");
    }

    /**
     * Test the implementation of NewsServiceInterface from IoC.
     */
    public function testImplementationFromIoC() {
        try {
            /* @var $newsService NewsServiceInterface */
            $newsService = static::$container->get("ns_core.news_service");
            $this->assertInstanceOf(NewsServiceInterface::class, $newsService, "1. The service has to be of type [NewsServiceInterface].");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception not expected: " . $exc->getMessage());
        }
    }

    /**
     * Test of the "findAllOnlineForPage" method.
     */
    public function testFindLastNewsPublished() {
        $newsContents = $this->object->findLastNewsPublished(2, "fr");
        $this->assertCount(2, $newsContents, "1. The collection has to contain 2 entities.");

        $newsContentsRu = $this->object->findLastNewsPublished(2, "ru");
        $this->assertCount(0, $newsContentsRu, "0. The collection has to contain 0 entity.");
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage must be of the type integer, string given
     */
    public function testFindLastNewsPublishedWithBadLimitParameter() {
        $this->object->findLastNewsPublished("fr", null);
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage must be of the type string, null given
     */
    public function testFindLastNewsPublishedWithBadLanguageParameter() {
        $this->object->findLastNewsPublished(2, null);
    }

    /**
     * @expectedException \Com\Nairus\CoreBundle\Exception\LocaleError
     * @expectedExceptionMessage "ru" locale is not available.
     *
     * @covers NewsServiceInterface::findLastNewsPublished
     */
    public function testFindLastNewsPublishedWithLanguageNotAvailable() {
        /* @var $newsService NewsServiceInterface */
        $newsService = static::$container->get("ns_core.news_service");
        $newsService->findLastNewsPublished(2, "ru");
    }

    /**
     * Test "findLastNewsPublished" method.
     * .
     * @covers NewsServiceInterface::findContentForNewsId
     */
    public function testFindContentForNewsId() {
        /* @var $newsRepository NewsRepository */
        $newsRepository = static::$em->getRepository(NSCoreBundle::NAME . ":News");
        $news = $newsRepository->findOneBy(["published" => true]);
        $contentFr = $this->object->findContentForNewsId($news, "fr");
        $this->assertNotNull($contentFr, "1. The news has to have a content for [fr] locale.");

        $contentRu = $this->object->findContentForNewsId($news, "ru");
        $this->assertNull($contentRu, "2. The news hasn't to have a content for [ru] locale.");
    }

    /**
     * @expectedException \Com\Nairus\CoreBundle\Exception\LocaleError
     * @expectedExceptionMessage "ru" locale is not available.
     */
    public function testFindContentForNewsIdWithBadLocale() {
        /* @var $newsRepository NewsRepository */
        $newsRepository = static::$em->getRepository(NSCoreBundle::NAME . ":News");
        $news = $newsRepository->findOneBy(["published" => true]);

        /* @var $newsService NewsServiceInterface */
        $newsService = static::$container->get("ns_core.news_service");
        $newsService->findContentForNewsId($news, "ru");
    }

}
