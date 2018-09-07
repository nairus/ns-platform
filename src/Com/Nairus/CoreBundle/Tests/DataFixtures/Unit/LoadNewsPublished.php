<?php

namespace Com\Nairus\CoreBundle\Tests\DataFixtures\Unit;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Fixture for published news.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LoadNewsPublished implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void {
        // Create parent News
        $firstNews = new News();
        $firstNews->setPublished(true);

        // Create contents
        $firstContentFr = new NewsContent();
        $firstContentFr
                ->setNews($firstNews)
                ->setTitle("Titre 2 FR")
                ->setDescription("Description 2 FR")
                ->setLink("http://www.news.fr")
                ->setLocale("fr");
        $firstNews->addContent($firstContentFr);
        $manager->persist($firstNews);
        $manager->flush();

        // We make a pause for having not the same creation date.
        sleep(1);

        $secondNews = new News();
        $secondNews->setPublished(true);

        // Create contents
        $secondContentFr = new NewsContent();
        $secondContentFr
                ->setNews($secondNews)
                ->setTitle("Titre 1 FR")
                ->setDescription("Description 1 FR")
                ->setLink("http://www.news.fr")
                ->setLocale("fr");
        $secondContentEn = new NewsContent();
        $secondContentEn
                ->setDescription("Description EN")
                ->setLink("http://www.news.com")
                ->setLocale("en")
                ->setTitle("Title EN")
                ->setNews($secondNews);
        $secondNews->addContent($secondContentFr);
        $secondNews->addContent($secondContentEn);
        $manager->persist($secondNews);
        $manager->flush();
    }

    /**
     * Remove the tests set.
     *
     * @param EntityManagerInterface $manager The manager instance.
     *
     * @return void
     */
    public function remove(EntityManagerInterface $manager): void {
        $dql = "SELECT n FROM " . NSCoreBundle::NAME . ":News n WHERE n.published = 1";
        $newsList = $manager
                ->createQuery($dql)
                ->getResult();

        foreach ($newsList as $news) {
            $manager->remove($news);
        }
        $manager->flush();
    }

}
