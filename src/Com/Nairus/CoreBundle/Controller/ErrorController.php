<?php

namespace Com\Nairus\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as TwigErrorController;

/**
 * Override the TwigBundle error controller to set the correct locale to the view.
 */
class ErrorController extends TwigErrorController {

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

        return parent::showAction($request, $exception, $logger);
    }

}
