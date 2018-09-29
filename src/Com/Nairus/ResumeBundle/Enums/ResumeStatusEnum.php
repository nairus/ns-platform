<?php

namespace Com\Nairus\ResumeBundle\Enums;

/**
 * Classe d'énumération des status de Resume.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
abstract class ResumeStatusEnum {

    const OFFLINE_INCOMPLETE = 0;
    const OFFLINE_TO_PUBLISHED = -1;
    const ONLINE = 1;

    /**
     * List of status icon classes.
     *
     * @var array <string>
     */
    private static $iconClasses = [
        self::OFFLINE_INCOMPLETE => "fas fa-thermometer-quarter",
        self::OFFLINE_TO_PUBLISHED => "fas fa-thermometer-half",
        self::ONLINE => "fas fa-thermometer-full",
    ];

    /**
     * Liste of i18n key labels.
     *
     * @var array <string>
     */
    private static $labelKeys = [
        self::OFFLINE_INCOMPLETE => "resume.status.offline",
        self::OFFLINE_TO_PUBLISHED => "resume.status.to-published",
        self::ONLINE => "resume.status.online",
    ];

    /**
     * Return the icon class for the current resume status.
     *
     * @param int $status The resume status.
     *
     * @return string
     *
     * @throws InvalidArgumentException In case of bad status.
     */
    public static final function getIconClass(int $status): string {
        if (!array_key_exists($status, self::$iconClasses)) {
            throw new \InvalidArgumentException("The resume status [$status] does not exist!");
        }

        return self::$iconClasses[$status];
    }

    /**
     * Return the label key for the current resume status.
     *
     * @param int $status The resume status.
     *
     * @return string
     */
    public static final function getLabelKey(int $status): string {
        if (!array_key_exists($status, self::$labelKeys)) {
            throw new \InvalidArgumentException("The resume status [$status] does not exist!");
        }

        return self::$labelKeys[$status];
    }

}
