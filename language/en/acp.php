<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_NEXTCLOUDCALENDAR_TITLE' => 'Nextcloud calendar',
    'ACP_NEXTCLOUDCALENDAR_SETTINGS' => 'Settings',
    'ACP_NEXTCLOUDCALENDAR_ENABLED' => 'Enable calendar submissions',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL' => 'CalDAV calendar URL',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL_EXPLAIN' => 'Full Nextcloud calendar URL, e.g. https://cloud.example.org/remote.php/dav/calendars/calendar-user/community-calendar/',
    'ACP_NEXTCLOUDCALENDAR_USERNAME' => 'Technical user',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD' => 'App password or password',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD_EXPLAIN' => 'Leave empty to keep the current password.',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE' => 'Timezone',
    'ACP_NEXTCLOUDCALENDAR_TEST' => 'Test connection',
    'ACP_NEXTCLOUDCALENDAR_TEST_OK' => 'Connection successful.',
]);
