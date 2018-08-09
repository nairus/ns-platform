<?php

namespace Com\Nairus\ResumeBundle\Enums;

/**
 * Classe d'énumération des status de Resume.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */

abstract class ResumeStatusEnum
{
    const OFFLINE_INCOMPLETE = 0;
    const OFFLINE_TO_PUBLISHED = -1;
    const ONLINE = 1;
}
