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
     * Return <code>true</code> if the entity has a translation for the current locale, <code>false</code> otherwise.
     *
     * @param string $locale The current locale.
     *
     * @return bool <code>true</code> if the entity has a translation for the current field, <code>false</code> otherwise
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
