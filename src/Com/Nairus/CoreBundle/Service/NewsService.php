<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Dto\NewsPaginatorDto;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Exception\LocaleError;
use Com\Nairus\CoreBundle\Exception\PaginatorException;
use Com\Nairus\CoreBundle\Repository\NewsRepository;
use Com\Nairus\CoreBundle\Repository\NewsContentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementation of News service.
 *
 * @author nairus
 */
class NewsService implements NewsServiceInterface {

    /**
     * @var NewsRepository
     */
    private $newsRepository;

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
        $this->newsRepository = $entityManager->getRepository(NSCoreBundle::NAME . ":News");
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

    /**
     * {@inheritDoc}
     */
    public function findNewsForPage(int $page, int $limit): NewsPaginatorDto {
        // Bad page argument
        if ($page < 1) {
            // Throws an exception.
            throw new PaginatorException($page, "Bad page [$page] for new list");
        }

        $newsPaginatorDto = new NewsPaginatorDto();

        // Calculate the offset for the current page.
        $offset = ($page - 1) * $limit;

        // Get the doctrine Paginator.
        $newsPaginator = $this->newsRepository->findNewsForPage($offset, $limit);
        $entities = $newsPaginator->getIterator()->getArrayCopy();
        $missingTranslations = [];

        // Get the available locales array to compare.
        $availableLocales = array_keys($this->availableLocales);

        // Build the missingTranslation map.
        foreach ($entities as /* @var $entity News */ $entity) {
            $contents = $entity->getContents();
            $contentsLocale = array_map(function(NewsContent $content) {
                return $content->getLocale();
            }, $contents->toArray());

            $missingTranslations[$entity->getId()] = array_diff($availableLocales, $contentsLocale);
        }

        // Get the total of entities.
        $total = $newsPaginator->count();

        // Calculate the number of pages.
        $pages = ceil($total / $limit);

        $newsPaginatorDto->setCurrentPage($page)
                ->setPages($pages)
                ->setEntities($entities)
                ->setMissingTranslations($missingTranslations);
        return $newsPaginatorDto;
    }

}
