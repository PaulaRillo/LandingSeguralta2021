<?php
defined('ABSPATH') || exit;

function duplicator_pro_get_home_path()
{
    static $homePath = null;
    if (is_null($homePath)) {
        $homePath = DupProSnapLibIOU::safePathUntrailingslashit(DupProSnapLibUtilWp::getHomePath(), true);
    }
    return $homePath;
}
