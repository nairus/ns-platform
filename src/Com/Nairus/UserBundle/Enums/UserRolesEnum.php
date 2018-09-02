<?php

namespace Com\Nairus\UserBundle\Enums;

/**
 * Enum class for user's roles.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class UserRolesEnum {

    /**
     * Définition des rôles d'utilisateur.
     */
    const USER = "ROLE_USER";
    const AUTHOR = "ROLE_AUTHOR";
    const MODERATOR = "ROLE_MODERATOR";
    const ADMIN = "ROLE_ADMIN";
    const SUPER_ADMIN = "ROLE_SUPER_ADMIN";

    /**
     * Constante pour définir si un utilisateur est authentifié par cookie ou par login.
     */
    const IS_AUTHENTICATED_REMEMBERED = "IS_AUTHENTICATED_REMEMBERED";
    const IS_AUTHENTICATED_FULLY = "IS_AUTHENTICATED_FULLY";

    /**
     * Liste des rôles pour les utilisateurs.
     *
     * @return array
     */
    public static function getRolesList() {
        return [
            self::AUTHOR => "Author",
            self::USER => "User",
            self::MODERATOR => "Moderator",
            self::ADMIN => "Admin",
            self::SUPER_ADMIN => "Super Admin",
        ];
    }

}
