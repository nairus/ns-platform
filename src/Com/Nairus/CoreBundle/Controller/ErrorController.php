<?php

namespace Com\Nairus\CoreBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as TwigErrorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Error controller.
 *
 * Overrided TwigBundle error controller to set the correct locale to the view.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ErrorController extends TwigErrorController {

    /**
     * Display an error page.
     *
     * @param Request              $request   The current service.
     * @param FlattenException     $exception The caught exception.
     * @param DebugLoggerInterface $logger    The logger service.
     *
     * @return Response
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): Response {
        // Force the locale to test transalation.
        $locale = $request->get("lg", null);

        // If the request uri begin by a locale.
        $requestUri = $request->getRequestUri();
        $matches = [];
        if (preg_match("~^/(en|fr)/.*$~", $requestUri, $matches)) {
            $request->setLocale($matches[1]);
        } else if (null !== $locale) {
            $request->setLocale($locale);
        }

        if ($exception->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $logger->error("An error occured while request `{request}`: {message}", [
                "request" => $request->getRequestUri(),
                "message" => $exception->getMessage()
            ]);
        } else {
            $logger->info("An error occured while request `{request}`: {message}", [
                "request" => $request->getRequestUri(),
                "message" => $exception->getMessage()
            ]);
        }

        return parent::showAction($request, $exception, $logger);
    }

}
