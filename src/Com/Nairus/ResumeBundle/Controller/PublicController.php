<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller {

    private const NAME = NSResumeBundle::NAME . ':' . "Public";

    /**
     * Index controller.
     *
     * @return Response
     */
    public function indexAction(Request $request, $page): Response {
        // Bug chrome
        if ("" === $page) {
            $page = 1;
        }

        $datesFormat = $this->container->getParameter("dates_format");
        $maxCardPerPage = $this->container->getParameter("max-cards-per-page");

        return $this->render(self::NAME . ':index.html.twig', [
                    "currentPage" => $page,
                    "pages" => 10,
                    "today" => new \DateTimeImmutable(),
                    "dateFormat" => $datesFormat[$request->getLocale()],
                    "maxCardPerPage" => $maxCardPerPage
        ]);
    }

}
