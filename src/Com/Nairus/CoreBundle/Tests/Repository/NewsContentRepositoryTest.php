<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\CoreBundle\Tests\DataFixtures\Unit\LoadNewsPublished;

/**
 * Test of NewsContent Repository
 *
 * @author nairus
 */
class NewsContentRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var NewsContentRepository
     */
    private static $repository;

    /**
     * @var NewsRepository
     */
    private static $newsRepository;

    /**
     * @var LoadNewsPublished
     */
    private static $loadNewsPublished;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSCoreBundle::NAME . ":NewsContent");
        static::$newsRepository = static::$em->getRepository(NSCoreBundle::NAME . ":News");
        static::$loadNewsPublished = new LoadNewsPublished();
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        // Create parent News
        $news = new News();
        static::$em->persist($news);
        static::$em->flush();

        // Create contents
        $contentFr = new NewsContent();
        $contentFr->setNews($news);
        $contentFr->setTitle("Titre FR")
                ->setDescription("Description FR")
                ->setLink("http://www.news.fr")
                ->setLocale("fr");
        $news->addContent($contentFr);

        $contentEn = new NewsContent();
        $contentEn->setNews($news);
        $contentEn->setTitle("Titre EN")
                ->setDescription("Description EN")
                ->setLink("http://www.news.com")
                ->setLocale("en");
        $news->addContent($contentEn);

        static::$em->flush();
        static::$em->clear();

        // Get the news content collection.
        $newsContentList = static::$repository->findAll();
        $this->assertCount(2, $newsContentList, "1.1 The collection has to contain 2 entities.");

        /* @var $firstContent NewsContent */
        $firstContent = $newsContentList[0];
        $this->assertNotNull($firstContent->getCreatedAt(), "2.1 The create date has to be set.");
        $this->assertNotNull($firstContent->getUpdatedAt(), "2.2 The update date has to be set.");

        // Update Test
        $createDate = $firstContent->getCreatedAt();
        $updateTimestamp = $firstContent->getUpdatedAt()->getTimestamp();
        $firstContent
                ->setDescription("New description")
                ->setLink("http://www.new.website.com")
                ->setTitle("New Title");
        $newsContentId = $firstContent->getId();
        sleep(1);
        static::$em->flush();

        /* @var $newsContentUpdated NewsContent */
        $newsContentUpdated = static::$repository->find($newsContentId);
        $this->assertSame($createDate->getTimestamp(), $newsContentUpdated->getCreatedAt()->getTimestamp(), "2.1 The create date has to be not changed.");
        $this->assertNotSame($updateTimestamp, $newsContentUpdated->getUpdatedAt()->getTimestamp(), "2.2 The update date has to be changed.");
        $this->assertSame("http://www.new.website.com", $newsContentUpdated->getLink(), "2.3 The link has to be modified.");
        $this->assertSame("New Title", $newsContentUpdated->getTitle(), "2.4 The title has to be modified.");
        $this->assertSame("New description", $newsContentUpdated->getDescription(), "2.5 The description has to be modified.");

        // Test Delete
        static::$em->remove($newsContentUpdated);
        static::$em->flush();
        static::$em->clear();

        $contentRemoved = static::$repository->find($newsContentId);
        $this->assertNull($contentRemoved, "3.1 The entity has to be removed.");

        // Get parent news.
        $parentNews = static::$newsRepository->find($news->getId());
        static::$em->remove($parentNews);
        static::$em->flush();
        static::$em->clear();

        $contents = static::$repository->findAll();
        $this->assertCount(0, $contents, "3.2 All the remaining content has to be removed.");
    }

    /**
     * Test the insert without foreign key.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */
    public function testInsertWithoutNews() {
        // Create contents
        $contentFr = new NewsContent();
        $contentFr->setTitle("Titre FR")
                ->setDescription("Description FR")
                ->setLink("http://www.news.fr");
        static::$em->persist($contentFr);
        static::$em->flush();
    }

    /**
     * Test the findLastNewsPublished methode
     */
    public function testFindLastNewsPublished() {
        // Init datas test set.
        static::$loadNewsPublished->load(static::$em);

        // Test with FR locale.
        $lastNewsFr = static::$repository->findLastNewsPublished(2, "fr");
        $this->assertCount(2, $lastNewsFr, "1.1 The FR collection has to contain 2 entities.");
        /* @var $firstContent NewsContent */
        $firstContent = $lastNewsFr[0];
        $this->assertEquals("Titre 2 FR", $firstContent->getTitle(), "1.1 The first entity has to be correct.");

        // Test with EN locale.
        $lastNewsEn = static::$repository->findLastNewsPublished(2, "en");
        $this->assertCount(1, $lastNewsEn, "2.1 The EN collection has to contain 1 entity.");

        // Remove datas test set.
        static::$loadNewsPublished->remove(static::$em);
    }

}
