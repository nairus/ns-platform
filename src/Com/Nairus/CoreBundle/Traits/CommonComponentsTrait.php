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
     * @param \Throwable $error   The error to log.
     * @param string     $context The error context (name of the service, controller, bundle ...).
     */
    protected function logError(\Throwable $error, string $context): void {
        $this->logger->error($error->getMessage(), [$context => $error]);
    }

}
