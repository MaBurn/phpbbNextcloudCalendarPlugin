<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACL_U_NEXTCLOUDCALENDAR_CREATE' => 'Peut proposer des evenements',
    'ACL_M_NEXTCLOUDCALENDAR_APPROVE' => 'Peut valider les evenements',
    'ACL_A_NEXTCLOUDCALENDAR' => 'Peut gerer le calendrier Nextcloud',
]);
