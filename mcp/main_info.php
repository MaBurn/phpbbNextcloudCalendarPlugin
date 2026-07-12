<?php

namespace maxbrenne\nextcloudcalendar\mcp;

class main_info
{
    public function module(): array
    {
        return [
            'filename' => '\maxbrenne\nextcloudcalendar\mcp\main_module',
            'title' => 'MCP_NEXTCLOUDCALENDAR_TITLE',
            'modes' => [
                'queue' => [
                    'title' => 'MCP_NEXTCLOUDCALENDAR_QUEUE',
                    'auth' => 'ext_maxbrenne/nextcloudcalendar && acl_m_nextcloudcalendar_approve',
                    'cat' => ['MCP_MAIN'],
                ],
            ],
        ];
    }
}
