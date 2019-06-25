<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Repository\NewsRepository;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadNewsPublished;

/**
 * Test of the News service.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
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

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$loadNewsPublished = new LoadNewsPublished();
        static::$loadNewsPublished->load(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass() {
        static::$loadNewsPublished->remove(static::$em);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->object = new NewsService(static::$em);
        $this->object->setAvailableLocales(["fr", "en", "ru"]);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * Test the implementation of NewsServiceInterface.
     */
    public function testImplementation(): void {
        $this->assertInstanceOf(NewsServiceInterface::class, $this->object, "1. The service has to be of type [NewsServiceInterface].");
    }

    /**
     * Test the implementation of NewsServiceInterface from IoC.
     */
    public function testImplementationFromIoC(): void {
        try {
            /* @var $newsService NewsServiceInterface */
            $newsService = static::$container->get("ns_core.news_service");
            $this->assertInstanceOf(NewsServiceInterface::class, $newsService, "1. The service has to be of type [NewsServiceInterface].");
            $this->assertInstanceOf(NewsService::class, $newsService, "1. The service has to be of type [NewsService].");
        } catch (\Exception | \Error $exc) {
            $this->fail("2. Exception not expected: " . $exc->getMessage());
        }
    }

    /**
     * Test of the `findAllOnlineForPage` method.
     */
    public function testFindLastNewsPublished(): void {
        $newsContents = $this->object->findLastNewsPublished(2, "fr");
        $this->assertCount(2, $newsContents, "1. The collection has to contain 2 entities.");

        $newsContentsRu = $this->object->findLastNewsPublished(2, "ru");
        $this->assertCount(0, $newsContentsRu, "0. The collection has to contain 0 entity.");
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage must be of the type integer, string given
     */
    public function testFindLastNewsPublishedWithBadLimitParameter(): void {
        $this->object->findLastNewsPublished("fr", null);
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage must be of the type string, null given
     */
    public function testFindLastNewsPublishedWithBadLanguageParameter(): void {
        $this->object->findLastNewsPublished(2, null);
    }

    /**
     * @expectedException \Com\Nairus\CoreBundle\Exception\LocaleError
     * @expectedExceptionMessage "ru" locale is not available.
     *
     * @covers Com\Nairus\CoreBundle\Service\NewsService::findLastNewsPublished
     */
    public function testFindLastNewsPublishedWithLanguageNotAvailable(): void {
        /* @var $newsService NewsServiceInterface */
        $newsService = static::$container->get("ns_core.news_service");
        $newsService->findLastNewsPublished(2, "ru");
    }

    /**
     * Test "findLastNewsPublished" method.
     * .
     * @covers Com\Nairus\CoreBundle\Service\NewsService::findContentForNewsId
     */
    public function testFindContentForNewsId(): void {
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
    public function testFindContentForNewsIdWithBadLocale(): void {
        /* @var $newsRepository NewsRepository */
        $newsRepository = static::$em->getRepository(NSCoreBundle::NAME . ":News");
        $news = $newsRepository->findOneBy(["published" => true]);

        /* @var $newsService NewsServiceInterface */
        $newsService = static::$container->get("ns_core.news_service");
        $newsService->findContentForNewsId($news, "ru");
    }

    /**
     * Test the "findNewsForPage" method.
     *
     * @covers Com\Nairus\CoreBundle\Service\NewsService::findNewsForPage
     */
    public function testFindNewsForPage(): void {
        // Test page 1
        $newsPaginatorDtoForPage1 = $this->object->findNewsForPage(1, 2);
        $entitiesForPage1 = $newsPaginatorDtoForPage1->getEntities();
        /* @var $ $firstNews \Com\Nairus\CoreBundle\Entity\News */
        $firstNews = $entitiesForPage1[0];
        $this->assertSame(1, $newsPaginatorDtoForPage1->getPages(), "1.1 The number of pages has to be correct.");
        $this->assertCount(2, $entitiesForPage1, "1.2 The collection has to contain 2 entities.");
        $this->assertSame(1, $newsPaginatorDtoForPage1->getCurrentPage(), "1.3 The current page has to be set.");

        // Test the missing translation algo.
        $missingTranslations = $newsPaginatorDtoForPage1->getMissingTranslations();
        $this->assertCount(2, $missingTranslations, "2.1 The missingTranslation property has to contain 1 entry.");
        $this->assertArrayHasKey($firstNews->getId(), $missingTranslations, "2.2 The key has to exist.");
        $this->assertCount(1, $missingTranslations[$firstNews->getId()], "2.3 One locale has to be missing.");
        $this->assertNotContains("fr", $missingTranslations[$firstNews->getId()], "2.4 The [fr] locale hasn't to be missing.");

        // Test page 2
        $newsPaginatorDtoForPage2 = $this->object->findNewsForPage(2, 2);
        $this->assertCount(0, $newsPaginatorDtoForPage2->getEntities(), "3.1 The page 2 must be empty.");
    }

    /**
     * Test the "findNewsForPage" method.
     *
     * @expectedException \Com\Nairus\CoreBundle\Exception\PaginatorException
     */
    public function testFindNewsForPageWithBadPage(): void {
        $this->object->findNewsForPage(0, 1);
    }

}
