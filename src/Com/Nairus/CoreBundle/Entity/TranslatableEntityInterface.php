<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;

/**
 * Interface for translatable entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface TranslatableEntityInterface {

    /**
     * Return `true` if the entity has a translation for the current locale, `false` otherwise.
     *
     * @param string $locale The current locale.
     *
     * @return bool `true` if the entity has a translation for the current field, `false` otherwise
     */
    public function hasTranslation(string $locale): bool;

    /**
     * Translation helper method.
     *
     * @param $locale The locale of the translation.
     *
     * @return TranslationEntityInterface the translation entity.
     */
    public function translate($locale = null): TranslationEntityInterface;
}
