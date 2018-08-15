<?php

namespace Com\Nairus\CoreBundle\Exception;

/**
 * Exception for paginator error.
 */
class PaginatorException extends \Exception {

    /**
     * Page requested.
     *
     * @var int
     */
    private $page;

    /**
     * The constructor.
     *
     * @param int        $page     The wrong page requested.
     * @param string     $message  The message of the exception.
     * @param int        $code     The code of the exception.
     * @parap \Throwable $previous The previous exception/error thrown.
     */
    public function __construct(int $page, string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->page = $page;
    }

    /**
     * Return the title of the resume.
     *
     * @return string
     */
    public function getPage(): int {
        return $this->page;
    }

}
