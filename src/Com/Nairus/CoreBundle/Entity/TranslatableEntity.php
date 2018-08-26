<?php

namespace Com\Nairus\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Interface for translatable entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface TranslatableEntity {

    /**
     * Add translation.
     *
     * @param AbstractTranslation $translation
     *
     * @return Education
     */
    public function addTranslation(AbstractPersonalTranslation $translation);

    /**
     * Remove translation.
     *
     * @param AbstractPersonalTranslation $translation
     *
     * @return boolean <code>TRUE</code> if this collection contained the specified element, <cod>FALSE</code> otherwise.
     */
    public function removeTranslation(AbstractPersonalTranslation $translation): bool;

    /**
     * Get translations.
     *
     * @return Collection
     */
    public function getTranslations(): Collection;

    /**
     * Return <code>true</code> if the entity has a translation for the current field, <code>false</code> otherwise.
     *
     * @param string $locale The translation locale.
     * @param string $field  The current field.
     *
     * @return bool <code>true</code> if the entity has a translation for the current field, <code>false</code> otherwise
     */
    public function hasTranslation(string $locale, string $field): bool;

    /**
     * Return the translation entity if exists, default field otherwise.
     *
     * @param string $locale The translation locale.
     * @param string $field  The current field.
     *
     * @return string
     */
    public function getTranslation(string $locale, string $field): string;
}
