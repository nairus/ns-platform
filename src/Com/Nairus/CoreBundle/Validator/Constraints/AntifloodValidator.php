<?php

namespace Com\Nairus\CoreBundle\Validator\Constraints;

use Com\Nairus\CoreBundle\Entity\IpTraceable;
use Com\Nairus\CoreBundle\Validator\Antifloodable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Antiflood validator.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AntifloodValidator extends ConstraintValidator {

    const BAD_REPOSITORY_ERROR = 1;
    const BAD_ENTITY_ERROR = 2;

    /**
     * Entity manager instance.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em The entity manager .
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint) {
        $repository = $this->em->getRepository(\get_class($entity));
        if (!$repository instanceof Antifloodable) {
            throw new ValidatorException("The repository has to be an instance of [Antifloodable]", static::BAD_REPOSITORY_ERROR);
        }

        if (!$entity instanceof IpTraceable) {
            throw new ValidatorException("The entity has to be an instance of [IpTraceable]", static::BAD_ENTITY_ERROR);
        }

        // Check if the client try to flood the server of message.
        $isFlood = $repository->isFlood($entity, $constraint->seconds);

        // If the flood attempt is proven, a violation is created.
        if ($isFlood) {
            $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
        }
    }

}
