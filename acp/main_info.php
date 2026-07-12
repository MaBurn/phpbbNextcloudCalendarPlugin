<?php

namespace maxbrenne\nextcloudcalendar\acp;

class main_info
{
    public function module(): array
    {
        return [
            'filename' => '\maxbrenne\nextcloudcalendar\acp\main_module',
            'title' => 'ACP_NEXTCLOUDCALENDAR_TITLE',
            'modes' => [
                'settings' => [
                    'title' => 'ACP_NEXTCLOUDCALENDAR_SETTINGS',
                    'auth' => 'ext_maxbrenne/nextcloudcalendar && acl_a_nextcloudcalendar',
                    'cat' => ['ACP_NEXTCLOUDCALENDAR_TITLE'],
                ],
            ],
        ];
    }
}
