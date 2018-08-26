<?php

namespace Com\Nairus\CoreBundle\Entity;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Abstract Translation Entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractTranslationEntity extends AbstractPersonalTranslation {

    /**
     * Convinient constructor
     *
     * @param string             $locale The locale of the translation.
     * @param string             $field  The field of the translation.
     * @param string             $value  The value of the translation.
     * @param TranslatableEntity $object The parent entity (required only in case of update).
     */
    public function __construct(string $locale, string $field, string $value, TranslatableEntity $object = null) {
        // Validate the instance of parent object if not null.
        if (null !== $object) {
            $this->validObjectClass($object);
        }

        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
        $this->setObject($object);
    }

    /**
     * Valid the class of parent object.
     *
     * @throws \TypeError In case of bad type.
     */
    abstract public function validObjectClass(TranslatableEntity $object): void;
}
