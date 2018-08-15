<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Exception\LocaleError;
use Com\Nairus\CoreBundle\Repository\NewsContentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementation of News service.
 *
 * @author nairus
 */
class NewsService implements NewsServiceInterface {

    /**
     * @var NewsContentRepository
     */
    private $newsContentRepository;

    /**
     * @var array
     */
    private $availableLocales;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The current entity manager.
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->newsContentRepository = $entityManager->getRepository(NSCoreBundle::NAME . ":NewsContent");
    }

    /**
     * {@inheritDoc}
     */
    public function findLastNewsPublished(int $limit, string $language) {
        // Throws a LocaleError if language passed is not available.
        if (!array_key_exists($language, $this->availableLocales)) {
            throw new LocaleError(
                    $language,
                    "\"$language\" locale is not available."
            );
        }

        return $this->newsContentRepository->findLastNewsPublished($limit, $language);
    }

    /**
     * Set available locales hash map (use with ioc).
     *
     * @param array $availableLocales
     *
     * @return void
     */
    public function setAvailableLocales(array $availableLocales): void {
        $this->availableLocales = [];
        foreach ($availableLocales as $locale) {
            $this->availableLocales[$locale] = TRUE;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findContentForNewsId(News $news, string $locale): ?NewsContent {
        // Throws a LocaleError if language passed is not available.
        if (!array_key_exists($locale, $this->availableLocales)) {
            throw new LocaleError(
                    $locale,
                    "\"$locale\" locale is not available."
            );
        }

        return $this->newsContentRepository->findOneBy(["news" => $news, "locale" => $locale]);
    }

}
