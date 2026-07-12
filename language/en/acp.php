<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_NEXTCLOUDCALENDAR_TITLE' => 'Nextcloud calendar',
    'ACP_NEXTCLOUDCALENDAR_SETTINGS' => 'Settings',
    'ACP_NEXTCLOUDCALENDAR_ENABLED' => 'Enable calendar submissions',
    'ACP_NEXTCLOUDCALENDAR_ENABLED_EXPLAIN' => 'Controls whether users may open the submission form and send new calendar suggestions. Moderators can still review existing pending suggestions.',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION' => 'Show frontend link',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_EXPLAIN' => 'Controls where users see the submission form link outside a post. The [nextcloudcalendar] placeholder inside posts is not affected.',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_NAVIGATION' => 'Main navigation',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_INDEX' => 'Board index above the forum list',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_FOOTER' => 'Footer',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_NONE' => 'Do not show an automatic link',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL' => 'CalDAV calendar URL',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL_EXPLAIN' => 'Full Nextcloud calendar URL, e.g. https://cloud.example.org/remote.php/dav/calendars/calendar-user/community-calendar/',
    'ACP_NEXTCLOUDCALENDAR_USERNAME' => 'Technical user',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD' => 'App password or password',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD_EXPLAIN' => 'Leave empty to keep the current password.',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE' => 'Timezone',
    'ACP_NEXTCLOUDCALENDAR_TEST' => 'Test connection',
    'ACP_NEXTCLOUDCALENDAR_TEST_OK' => 'Connection successful.',
]);
