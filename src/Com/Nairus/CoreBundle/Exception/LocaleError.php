<?php

namespace Com\Nairus\CoreBundle\Exception;

/**
 * Locale Error.
 *
 * @author nairus
 */
class LocaleError extends \Exception {

    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale, string $message = "", int $code = 0, Throwable $previous = null) {
        $this->locale = $locale;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Return the locale not available.
     *
     * @return string
     */
    public function getLocale(): string {
        return $this->locale;
    }

}
