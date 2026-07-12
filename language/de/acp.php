<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_NEXTCLOUDCALENDAR_TITLE' => 'Nextcloud-Kalender',
    'ACP_NEXTCLOUDCALENDAR_SETTINGS' => 'Einstellungen',
    'ACP_NEXTCLOUDCALENDAR_ENABLED' => 'Kalendereinreichungen aktivieren',
    'ACP_NEXTCLOUDCALENDAR_ENABLED_EXPLAIN' => 'Steuert, ob Benutzer das Einreichungsformular öffnen und neue Kalendereinträge vorschlagen dürfen. Moderatoren können vorhandene wartende Vorschläge weiterhin prüfen.',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL' => 'CalDAV-Kalender-URL',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL_EXPLAIN' => 'Vollständige URL des Nextcloud-Kalenders, z. B. https://cloud.example.org/remote.php/dav/calendars/kalender-user/vereinskalender/',
    'ACP_NEXTCLOUDCALENDAR_USERNAME' => 'Technischer Benutzer',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD' => 'App-Passwort oder Passwort',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD_EXPLAIN' => 'Leer lassen, um das vorhandene Passwort beizubehalten.',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE' => 'Zeitzone',
    'ACP_NEXTCLOUDCALENDAR_TEST' => 'Verbindung testen',
    'ACP_NEXTCLOUDCALENDAR_TEST_OK' => 'Verbindung erfolgreich.',
]);
