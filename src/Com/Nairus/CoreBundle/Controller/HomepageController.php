<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Service\NewsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Homepage controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class HomepageController extends Controller {

    /**
     * @var NewsServiceInterface
     */
    private $newsService;

    /**
     * Constructor.
     *
     * @param NewsServiceInterface $newsService News service.
     */
    public function __construct(NewsServiceInterface $newsService) {
        $this->newsService = $newsService;
    }

    /**
     * Homepage controller.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function indexAction(Request $request): Response {
        $limit = $this->container->getParameter("ns_core.last_news_limit");
        $locale = $request->getLocale();
        $lastNewsContentList = $this->newsService->findLastNewsPublished($limit, $locale);
        return $this->render(NSCoreBundle::NAME . ':Homepage:index.html.twig', ["lastNewsContentList" => $lastNewsContentList]);
    }

}
