<?php

namespace Com\Nairus\CoreBundle\Exception;

use Symfony\Component\HttpKernel\Exception\GoneHttpException as SFGoneHttpException;

/**
 * Custom HTTP exception for 410-GONE redirection.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class GoneHttpException extends SFGoneHttpException {

    /**
     * Url to redirect.
     *
     * @var string
     */
    private $redirectUrl;

    /**
     * Constructor
     *
     * @param string     $redirectUrl The url to redirect.
     * @param string     $message     The error message.
     * @param \Exception $previous    The previous exception.
     * @param int        $code        The error code.
     */
    public function __construct(string $redirectUrl, string $message = null, \Exception $previous = null, int $code = 0) {
        parent::__construct($message, $previous, $code);
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Get the url to redirect.
     *
     * @return the url to redirect.
     */
    public function getRedirectUrl() {
        return $this->redirectUrl;
    }

}
