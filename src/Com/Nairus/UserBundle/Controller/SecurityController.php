<?php

namespace Com\Nairus\UserBundle\Controller;

use Com\Nairus\UserBundle\Enums\UserRolesEnum;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Users administation controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SecurityController extends BaseController {

    /**
     * Constructor.
     *
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager = null) {
        parent::__construct($tokenManager);
    }

    /**
     * Login overrided action.
     *
     * @return Response
     */
    public function loginAction(Request $request) {
        // If the user is authenticated, redirect to his profile.
        if ($this->get('security.authorization_checker')->isGranted(UserRolesEnum::IS_AUTHENTICATED_REMEMBERED)) {
            return $this->redirectToRoute("fos_user_profile_show");
        }

        return parent::loginAction($request);
    }

}
