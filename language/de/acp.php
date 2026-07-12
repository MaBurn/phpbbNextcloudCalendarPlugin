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
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION' => 'Frontend-Link anzeigen',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_EXPLAIN' => 'Legt fest, wo Benutzer den Link zum Formular außerhalb eines Beitrags sehen. Die Kachel nutzt die Forenoptik des aktiven Themes. Der Platzhalter [nextcloudcalendar] in Beiträgen bleibt davon unabhängig.',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_NAVIGATION' => 'Hauptnavigation',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_QUICKLINKS' => 'Schnellzugriff-Menü',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_INDEX_BUTTON' => 'Startseite als Button über der Forenliste',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_INDEX_TILE' => 'Startseite als Kachel über der Forenliste',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_FOOTER' => 'Footer',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_NONE' => 'Keinen automatischen Link anzeigen',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_ICON' => 'FontAwesome-Icon',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_ICON_EXPLAIN' => 'FA-Klasse für Link und Kachel, z. B. fa-calendar-plus-o, fa-calendar oder calendar-check-o.',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL' => 'CalDAV-Kalender-URL',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL_EXPLAIN' => 'Vollständige URL des Nextcloud-Kalenders, z. B. https://cloud.example.org/remote.php/dav/calendars/kalender-user/vereinskalender/',
    'ACP_NEXTCLOUDCALENDAR_USERNAME' => 'Technischer Benutzer',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD' => 'App-Passwort oder Passwort',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD_EXPLAIN' => 'Leer lassen, um das vorhandene Passwort beizubehalten.',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE' => 'Zeitzone',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE_INVALID' => 'Die Zeitzone „%s“ ist kein gültiger PHP-Zeitzonen-Bezeichner. Die bisherige Zeitzone wurde beibehalten.',
    'ACP_NEXTCLOUDCALENDAR_TEST' => 'Verbindung testen',
    'ACP_NEXTCLOUDCALENDAR_TEST_OK' => 'Verbindung erfolgreich.',
]);
