<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Entity\Traits\IsNewTrait;
use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * Abstract class for Translatable entities
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractTranslatableEntity extends AbstractTranslatable implements TranslatableEntityInterface {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Current locale (not mapped in database).
     *
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * The current translation entity (not mapped in database).
     *
     * @var TranslationEntityInterface
     */
    private $currentTranslation;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->translations = new ArrayCollection();
    }

    use IsNewTrait;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Return the current locale of the entity.
     *
     * @return string|null
     */
    public function getCurrentLocale(): ?string {
        return $this->currentLocale;
    }

    /**
     * Define the current locale of the entity.
     *
     * @param string $currentLocale
     */
    public function setCurrentLocale(string $currentLocale) {
        $this->currentLocale = $currentLocale;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTranslation(TranslationInterface $translation) {
        $this->validateTranslationEntity($translation);
        return parent::addTranslation($translation);
    }

    /**
     * {@inheritDoc}
     */
    public function removeTranslation(TranslationInterface $translation) {
        $this->validateTranslationEntity($translation);
        return parent::removeTranslation($translation);
    }

    /**
     * {@inheritDoc}
     */
    public function translate($locale = null): TranslationEntityInterface {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }

        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        if (!$translation = $this->translations->get($locale)) {
            $translationEntityClass = static::getTranslationEntityClass();
            $translation = new $translationEntityClass();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }

    /**
     * {@inheritDoc}
     */
    public function hasTranslation(string $locale): bool {
        $translations = $this->getTranslations();
        return $translations->containsKey($locale);
    }

    /**
     * Return the entity class for translations.
     *
     * @return string
     */
    abstract public static function getTranslationEntityClass(): string;

    /**
     * Validate the translation entity.
     *
     * @param TranslationEntityInterface $translation The translation entity to validate.
     *
     * @throw \TypeError In case of bad translation type.
     */
    abstract protected function validateTranslationEntity(TranslationEntityInterface $translation): void;
}
