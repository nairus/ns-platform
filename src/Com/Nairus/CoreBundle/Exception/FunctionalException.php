<?php

namespace Com\Nairus\CoreBundle\Exception;

/**
 * Functional exception for user message.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class FunctionalException extends \Exception {

    /**
     * Key the translator service.
     *
     * @var string
     */
    private $translationKey;

    /**
     * Constructor.
     *
     * @param string     $translationKey The translator service key.
     * @param string     $message        The exception message (for logging).
     * @param int        $code           The error code.
     * @param \Throwable $previous       The parent exception.
     */
    public function __construct(string $translationKey, string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->translationKey = $translationKey;
    }

    /**
     * Return the translation key.
     *
     * @return string
     */
    public function getTranslationKey(): string {
        return $this->translationKey;
    }

}
