<?php

namespace Com\Nairus\CoreBundle\Repository;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;

/**
 * Test of News Repository
 *
 * @author nairus
 */
class NewsRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var NewsRepository
     */
    private static $repository;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSCoreBundle::NAME . ":News");
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        $content = new NewsContent();
        $content->setTitle("Titre FR")
                ->setDescription("Description FR")
                ->setLink("http://www.news.fr")
                ->setLocale("fr");
        $news = new News();
        $news->addContent($content);
        $content->setNews($news);
        static::$em->persist($news);
        static::$em->persist($content);
        static::$em->flush();
        static::$em->clear();

        // Get the news collection.
        $newsList = static::$repository->findAll();
        $this->assertCount(1, $newsList, "1.1 The collection has to contain 1 entity.");
        /* @var $firstNews News */
        $firstNews = $newsList[0];
        $this->assertEquals($news->getId(), $firstNews->getId(), "1.2 The id has to be equals");
        $this->assertCount(1, $firstNews->getContents(), "1.3 The news has to contain 1 [NewsContent] entity");
        $this->assertNotNull($news->getCreatedAt(), "1.4 The create date has to be set.");
        $this->assertNotNull($news->getUpdatedAt(), "1.5 The update date has to be set.");
        $this->assertNull($news->getPublishedAt(), "1.6 The published date has  to be NULL.");

        // Update Test
        $createDate = $news->getCreatedAt();
        $updateDate = $news->getUpdatedAt();
        $firstNews->setPublished(true);
        $newsId = $firstNews->getId();
        sleep(1);
        static::$em->flush();
        static::$em->clear();

        /* @var $newsUpdated News */
        $newsUpdated = static::$repository->find($newsId);
        $this->assertSame($createDate->getTimestamp(), $newsUpdated->getCreatedAt()->getTimestamp(), "2.1 The create date has to be not changed.");
        $this->assertNotSame($updateDate->getTimestamp(), $newsUpdated->getUpdatedAt()->getTimestamp(), "2.2 The update date has to be changed.");
        $this->assertNotNull($newsUpdated->getPublishedAt(), "2.3 The published date has to be set.");
        $this->assertTrue($newsUpdated->getPublished(), "2.4 The news has to be published");

        // Test Delete
        static::$em->remove($newsUpdated);
        static::$em->flush();
        static::$em->clear();

        $newsRemoved = static::$repository->find($newsId);
        $this->assertNull($newsRemoved, "3.1 The entity has to be removed.");
    }

}
