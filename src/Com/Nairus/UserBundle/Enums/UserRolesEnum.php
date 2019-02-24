<?php

namespace Com\Nairus\UserBundle\Enums;

/**
 * Enum class for user's roles.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class UserRolesEnum {

    /**
     * Users roles definition.
     */
    const USER = "ROLE_USER";
    const AUTHOR = "ROLE_AUTHOR";
    const MODERATOR = "ROLE_MODERATOR";
    const ADMIN = "ROLE_ADMIN";
    const SUPER_ADMIN = "ROLE_SUPER_ADMIN";

    /**
     * Constant to define if an user is authenticated by cookie or login.
     */
    const IS_AUTHENTICATED_REMEMBERED = "IS_AUTHENTICATED_REMEMBERED";
    const IS_AUTHENTICATED_FULLY = "IS_AUTHENTICATED_FULLY";

    /**
     * Constructor.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {
        // this class should not be instanciated.
    }

    /**
     * Get the users roles list.
     *
     * @return array
     */
    public static function getRolesList(): array {
        return [
            self::AUTHOR => "Author",
            self::USER => "User",
            self::MODERATOR => "Moderator",
            self::ADMIN => "Admin",
            self::SUPER_ADMIN => "Super Admin",
        ];
    }

}
