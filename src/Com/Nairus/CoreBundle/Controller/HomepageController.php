<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HomepageController extends Controller {

    /**
     * Homepage controller.
     *
     * @return Response
     */
    public function indexAction(): Response {
        return $this->render(NSCoreBundle::NAME . ':Homepage:index.html.twig');
    }

    /**
     * Contact controller.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function contactAction(Request $request): Response {
        $request->getSession()->getFlashBag()->add(
                'info',
                $this->getTranslator()->trans("flashes.homepage.contact.page-unavailable", [], NSCoreBundle::NAME)
        );

        return $this->redirectToRoute("ns_core_homepage");
    }

    /**
     * Retourne une instance du service de traduction.
     *
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    private function getTranslator() {
        return $this->get("translator");
    }

}
