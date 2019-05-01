<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\CoreBundle\Exception\GoneHttpException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Constants\ExceptionCodeConstants;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Exception\ResumeListException;
use Com\Nairus\ResumeBundle\Helper\ResumeHelperInterface;
use Com\Nairus\ResumeBundle\Service\ResumeServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

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
     * Resume helper.
     *
     * @var ResumeHelperInterface
     */
    private $resumeHelper;

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
    public function __construct(ResumeServiceInterface $resumeService, ResumeHelperInterface $resumeHelper, LoggerInterface $logger) {
        $this->resumeService = $resumeService;
        $this->resumeHelper = $resumeHelper;
        $this->logger = $logger;
    }

    /**
     * List of all resumes.
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
            if ($exc->getCode() == ExceptionCodeConstants::PAGE_NOT_FOUND) {
                throw $this->createNotFoundException($exc->getMessage(), $exc);
            } else {
                throw new BadRequestHttpException($exc->getMessage(), $exc);
            }
        } catch (\Throwable $exc) {
            $this->logger->error("An unkown error occured in {controller}.{action}({page})",
                    ["controller" => self::NAME, "action" => "indexAction", "page" => $page]);
            throw new ServiceUnavailableHttpException(null, null, $exc);
        }
    }

    /**
     * Details of a resume.
     *
     * @param Request $request The current HTTP request.
     * @param int     $id      The current resume id.
     *
     * @return Response
     */
    public function detailsAction(Request $request, int $id, string $slug): Response {
        try {
            $locale = $request->getLocale();

            // Get the details of the resume for the current locale.
            $dto = $this->resumeService->getDetailsForResumeId($id, $locale);

            // 410 redirect if the resume is offline.
            if (ResumeStatusEnum::ONLINE !== $dto->getResume()->getStatus()) {
                throw new GoneHttpException($this->generateUrl("ns_resume_homepage"), "The resume [$id] is offline for locale [$locale]");
            }

            // If the resume is incomplete, do a 410 redirection.
            if (!$this->resumeHelper->isComplete($dto)) {
                throw new GoneHttpException($this->generateUrl("ns_resume_homepage"), "The resume [$id] is incomplete for locale [$locale]");
            }

            // 301 redirect if the the slug is not ok
            if ($slug !== $dto->getResume()->getSlug()) {
                return $this->redirect($this->generateUrl("ns_resume_details", ['id' => $id, 'slug' => $dto->getResume()->getSlug()]),
                                Response::HTTP_MOVED_PERMANENTLY);
            }

            $datesFormat = $this->container->getParameter("dates_format");
            return $this->render(self::NAME . ':details.html.twig', [
                        "dto" => $dto,
                        "dateFormat" => $datesFormat[$request->getLocale()],
            ]);
        } catch (\Doctrine\ORM\EntityNotFoundException $exc) {
            throw new GoneHttpException($this->generateUrl("ns_resume_homepage"), "The resume [$id] does not exist for the locale [$locale]", $exc);
        }
    }

}
