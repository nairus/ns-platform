<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\CoreBundle\Entity\Traits\IsNewTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Skill entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_skill")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\SkillRepository")
 */
class Skill {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Skill
     */
    public function setTitle(string $title): Skill {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    use IsNewTrait;
}
