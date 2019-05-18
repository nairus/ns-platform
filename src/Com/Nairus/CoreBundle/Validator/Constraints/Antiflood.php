<?php

namespace Com\Nairus\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Antiflood constraint.
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Antiflood extends Constraint {

    /**
     * Unique key for the constaint (uuid v4).
     *
     * @var string
     */
    const IS_FLOOD_ERROR = "089e7558-8311-46b2-882a-a8d9a6a03a36";

    /**
     * Seconds option name.
     *
     * @var string
     */
    const OPTION_SECONDS = "seconds";

    /**
     * The default error message.
     *
     * @var string
     */
    public $message = "ns_core.antiflood.default_message";

    /**
     * The seconds to detect a flood request.
     *
     * @var int
     */
    public $seconds;

    /**
     * List of error names.
     *
     * @var array
     */
    protected static $errorNames = [
        self::IS_FLOOD_ERROR => 'IS_FLOOD_ERROR',
    ];

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions() {
        return [self::OPTION_SECONDS];
    }

    /**
     * {@inheritDoc}
     */
    public function validatedBy() {
        return "ns_core.validator.antiflood";
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption() {
        return self::OPTION_SECONDS;
    }

}
