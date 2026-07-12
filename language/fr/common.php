<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'NEXTCLOUDCALENDAR_NAV_REQUEST' => 'Proposer un evenement',
    'NEXTCLOUDCALENDAR_TILE_EXPLAIN' => 'Envoyer une nouvelle date et la publier dans le calendrier apres validation.',
    'NEXTCLOUDCALENDAR_PAGE_TITLE' => 'Proposer un evenement',
    'NEXTCLOUDCALENDAR_EVENT_TITLE' => 'Titre',
    'NEXTCLOUDCALENDAR_EVENT_DESCRIPTION' => 'Description',
    'NEXTCLOUDCALENDAR_EVENT_LOCATION' => 'Lieu',
    'NEXTCLOUDCALENDAR_EVENT_START' => 'Debut',
    'NEXTCLOUDCALENDAR_EVENT_END' => 'Fin',
    'NEXTCLOUDCALENDAR_SUBMIT' => 'Envoyer',
    'NEXTCLOUDCALENDAR_SUBMITTED' => 'Votre evenement a ete envoye et attend validation.',
    'NEXTCLOUDCALENDAR_DISABLED' => "Les propositions d'evenements sont actuellement desactivees par un administrateur. Les moderateurs peuvent toujours traiter les propositions en attente.",
    'NEXTCLOUDCALENDAR_NOT_AUTHORISED' => "Vous n'etes pas autorise a proposer des evenements.",
    'NEXTCLOUDCALENDAR_FORM_INVALID' => 'Veuillez verifier les champs indiques.',
    'NEXTCLOUDCALENDAR_END_BEFORE_START' => 'La fin doit etre apres le debut.',
]);
