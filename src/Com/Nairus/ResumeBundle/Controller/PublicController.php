<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller {

    const NAME = "Public";

    /**
     * Index controller.
     *
     * @return Response
     */
    public function indexAction(): Response {
        return $this->render(NSResumeBundle::NAME . ':' . self::NAME . ':index.html.twig');
    }

}
