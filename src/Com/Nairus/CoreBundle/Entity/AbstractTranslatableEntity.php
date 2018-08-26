<?php

namespace Com\Nairus\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Abstract class for Translatable entities
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractTranslatableEntity implements TranslatableEntity {

    /**
     * Collection of translations.
     *
     * This property has to be mapped in the child class.
     */
    protected $translations;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->translations = new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    abstract public function addTranslation(AbstractPersonalTranslation $translation);

    /**
     * {@inheritDoc}
     */
    abstract public function removeTranslation(AbstractPersonalTranslation $translation): bool;

    /**
     * {@inheritDoc}
     */
    public function getTranslations(): Collection {
        return $this->translations;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslation(string $locale, string $field): string {
        // Searching the translation.
        foreach ($this->getTranslations() as /* @var $translation AbstractPersonalTranslation */ $translation) {
            if ($translation->getField() === $field && $translation->getLocale() === $locale) {
                return $translation->getContent();
            }
        }

        // Return the default value, if translation does not exist.
        $getter = 'get' . ucfirst($field);
        return $this->$getter();
    }

    /**
     * {@inheritDoc}
     */
    public function hasTranslation(string $locale, string $field): bool {
        foreach ($this->getTranslations() as /* @var $translation AbstractPersonalTranslation */ $translation) {
            if ($translation->getField() === $field && $translation->getLocale() === $locale) {
                return true;
            }
        }
        return false;
    }

}