<?php

namespace Com\Nairus\ResumeBundle\Enums;

/**
 * Classe d'énumération des roles des utilisateurs.
 *
 * @author nairus
 */
abstract class UserRolesEnum
{
    /**
     * Définition des rôles d'utilisateur.
     */
    const USER = "ROLE_USER";
    const AUTHOR = "ROLE_AUTHOR";
    const MODERATOR = "ROLE_MODERATOR";
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
    public static function getRolesList()
    {
        return [
            self::AUTHOR => "Author",
            self::USER => "User",
            self::MODERATOR => "Moderator",
            self::SUPER_ADMIN => "Super Admin",
        ];
    }
}
