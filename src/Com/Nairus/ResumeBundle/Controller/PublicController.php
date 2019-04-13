<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Constants\ExceptionCodeConstants;
use Com\Nairus\ResumeBundle\Exception\ResumeListException;
use Com\Nairus\ResumeBundle\Service\ResumeServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PublicController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":Public";

    /**
     * Resume service.
     *
     * @var ResumeServiceInterface
     */
    private $resumeService;

    /**
     * Logger service instance.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * Constructor.
     *
     * @param ResumeServiceInterface $resumeService resume service.
     * @param LoggerInterface        $logger        logger service.
     */
    public function __construct(ResumeServiceInterface $resumeService, LoggerInterface $logger) {
        $this->resumeService = $resumeService;
        $this->logger = $logger;
    }

    /**
     * Index controller.
     *
     * @param Request $request The current request.
     * @param integer $page    The current page.
     *
     * @return Response
     */
    public function indexAction(Request $request, $page): Response {
        try {
            // Bug chrome
            if ("" === $page) {
                $page = 1;
            }

            $datesFormat = $this->container->getParameter("dates_format");
            $defaultLocale = $this->container->getParameter('kernel.default_locale');
            $maxCardPerPage = $this->container->getParameter("ns_resume.max-cards-per-page");
            $dto = $this->resumeService->findAllOnlineForPage($page, $maxCardPerPage, $request->getLocale());

            return $this->render(self::NAME . ':index.html.twig', [
                        "currentPage" => $page,
                        "entities" => $dto->getEntities(),
                        "pages" => $dto->getPages(),
                        "today" => new \DateTimeImmutable(),
                        "dateFormat" => $datesFormat[$request->getLocale()],
                        "maxCardPerPage" => $maxCardPerPage,
                        "defaultLocale" => $defaultLocale,
            ]);
        } catch (ResumeListException $exc) {
            switch ($exc->getCode()) {
                case ExceptionCodeConstants::PAGE_NOT_FOUND:
                    throw $this->createNotFoundException($exc->getMessage(), $exc);

                case ExceptionCodeConstants::WRONG_PAGE:
                    throw new BadRequestHttpException($exc->getMessage(), $exc);

                default:
                    $this->logger->error("An unkown error occured in {controller}.{action}({page})",
                            ["controller" => self::NAME, "action" => "indexAction", "page" => $page]);
                    throw new ServiceUnavailableHttpException(null, null, $exc);
            }
        }
    }

}
