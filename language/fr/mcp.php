<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'MCP_NEXTCLOUDCALENDAR_TITLE' => 'Calendrier Nextcloud',
    'MCP_NEXTCLOUDCALENDAR_QUEUE' => 'Valider les evenements',
    'MCP_NEXTCLOUDCALENDAR_EMPTY' => "Aucun evenement n'attend de validation.",
    'MCP_NEXTCLOUDCALENDAR_SUBMITTED_BY' => 'Envoye par',
    'MCP_NEXTCLOUDCALENDAR_CREATED' => 'Envoye le',
    'MCP_NEXTCLOUDCALENDAR_APPROVE' => 'Valider',
    'MCP_NEXTCLOUDCALENDAR_REJECT' => 'Refuser',
    'MCP_NEXTCLOUDCALENDAR_APPROVED' => "L'evenement a ete valide et ajoute a Nextcloud.",
    'MCP_NEXTCLOUDCALENDAR_REJECTED' => "L'evenement a ete refuse.",
    'MCP_NEXTCLOUDCALENDAR_LAST_ERROR' => 'Derniere erreur Nextcloud',
]);
