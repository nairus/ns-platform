<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;

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

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSCoreBundle::NAME . ":NewsContent");
        static::$newsRepository = static::$em->getRepository(NSCoreBundle::NAME . ":News");
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

        $contentEn = new NewsContent();
        $contentEn->setNews($news);
        $contentEn->setTitle("Titre EN")
                ->setDescription("Description EN")
                ->setLink("http://www.news.com")
                ->setLocale("en");

        static::$em->persist($contentFr);
        static::$em->persist($contentEn);
        static::$em->flush();
        static::$em->clear();

        // Get the news content collection.
        $newsContentList = static::$repository->findAll();
        $this->assertCount(2, $newsContentList, "1.1 The collection has to contain 1 entity.");

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

    public function testInsertWithoutNews() {
        $this->markTestIncomplete("Todo");
    }

    public function testInsertUniqueNewsContent() {
        $this->markTestIncomplete("Todo");
    }

}
