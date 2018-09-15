<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Entity\TranslationEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * Abstract Translation Entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class AbstractTranslationEntity extends AbstractTranslation implements TranslationEntityInterface {

    /**
     * Locale (Only language code in ISO 639-1).
     *
     * @example fr for french language.
     *
     * @ORM\Column(name="locale", type="string", length=2)
     * @Prezent\Locale
     */
    protected $locale;

    /**
     * {@inheritDoc}
     */
    public function setTranslatable(TranslatableInterface $translatable = null) {
        // Prevent error in case of null args when remove method called from translatable object.
        if (null !== $translatable) {
            $this->validObjectClass($translatable);
        }
        return parent::setTranslatable($translatable);
    }

    /**
     * Valid the class of parent object.
     *
     * @throws \TypeError In case of bad type.
     */
    abstract protected function validObjectClass(TranslatableEntityInterface $object): void;
}
