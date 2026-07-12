<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_NEXTCLOUDCALENDAR_TITLE' => 'Calendrier Nextcloud',
    'ACP_NEXTCLOUDCALENDAR_SETTINGS' => 'Parametres',
    'ACP_NEXTCLOUDCALENDAR_ENABLED' => "Activer les propositions d'evenements",
    'ACP_NEXTCLOUDCALENDAR_ENABLED_EXPLAIN' => "Controle si les utilisateurs peuvent ouvrir le formulaire et envoyer de nouvelles propositions d'evenements. Les moderateurs peuvent toujours traiter les propositions en attente.",
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL' => 'URL du calendrier CalDAV',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL_EXPLAIN' => 'URL complete du calendrier Nextcloud, par ex. https://cloud.example.org/remote.php/dav/calendars/utilisateur-calendrier/calendrier-association/',
    'ACP_NEXTCLOUDCALENDAR_USERNAME' => 'Utilisateur technique',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD' => "Mot de passe d'application ou mot de passe",
    'ACP_NEXTCLOUDCALENDAR_PASSWORD_EXPLAIN' => 'Laisser vide pour conserver le mot de passe actuel.',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE' => 'Fuseau horaire',
    'ACP_NEXTCLOUDCALENDAR_TEST' => 'Tester la connexion',
    'ACP_NEXTCLOUDCALENDAR_TEST_OK' => 'Connexion reussie.',
]);
