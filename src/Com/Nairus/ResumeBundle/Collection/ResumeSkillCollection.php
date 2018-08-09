<?php

namespace Com\Nairus\ResumeBundle\Collection;

use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Collection des entités ResumeSkill.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class ResumeSkillCollection extends ArrayCollection
{
    /**
     * {@inheritDoc}
     */
    public function add($element): bool
    {
        $this->validType($element);
        return parent::add($element);
    }

    /**
     * Return the current element or FALSE.
     *
     * @return ResumeSkill|bool
     */
    public function current()
    {
        return parent::current();
    }

    /**
     * Return the first element or FALSE.
     *
     * @return ResumeSkill|bool
     */
    public function first()
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get($key) : ?ResumeSkill
    {
        return parent::get($key);
    }

    /**
     * Return the last element or FALSE.
     *
     * @return ResumeSkill|bool
     */
    public function last()
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset) : ?ResumeSkill
    {
        return parent::offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value) : ?bool
    {
        $this->validType($value);
        return parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value) : void
    {
        $this->validType($value);
        parent::set($key, $value);
    }

    /**
     * Valid the type of the element.
     *
     * @param ResumeSkill $entity The entity to verify.
     *
     * @throws \TypeError If this is the wrong type.
     */
    private function validType($entity) : void
    {
        if (!$entity instanceof ResumeSkill) {
            throw new \TypeError("Entité [ResumeSkill] attendue.");
        }
    }

}
