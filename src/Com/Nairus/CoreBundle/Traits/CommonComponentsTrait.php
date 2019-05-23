<?php

namespace Com\Nairus\CoreBundle\Traits;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Trait for commons components in controllers and services.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait CommonComponentsTrait {

    /**
     * Logger service.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Translator service.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Return the translation of a message.
     *
     * @param string $id     The id of the translation.
     * @param array  $params The parameters for the translation.
     * @param string $domain The file domain where the translations is stored.
     * @param string $locale The locale for the translation key.
     *
     * @return string
     */
    protected function getTranslation($id, $params = [], $domain = NSCoreBundle::NAME, $locale = null): string {
        return $this->translator->trans($id, $params, $domain, $locale);
    }

    /**
     * Log an error.
     *
     * @param string     $who           The class / bundle name where the error occured.
     * @param string     $how           The method where the error occured.
     * @param \Throwable $error         The exception/error to log.
     * @param string     $customMessage The custom message to log (replace the default message).
     * @param array      $customContext The custom context (merge with the default context: ['class' => $who, 'method' => $how]).
     *
     * @return void
     */
    protected function logError(string $who, string $how, \Throwable $error = null, string $customMessage = "", array $customContext = []): void {
        $context = array_merge(['class' => $who, 'method' => $how], $customContext);
        $message = empty($customMessage) ? "An error occured for class [{class}], method [{method}]" : $customMessage;
        if (null !== $error) {
            $context['error'] = $error->getMessage();
            $message .= ", message: {error}";
        }

        $this->logger->error($message, $context);
    }

}
