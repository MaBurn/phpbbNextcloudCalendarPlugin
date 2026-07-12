<?php

namespace maxbrenne\nextcloudcalendar\migrations;

class install extends \phpbb\db\migration\migration
{
    public function effectively_installed(): bool
    {
        return isset($this->config['nextcloudcalendar_version']) && version_compare($this->config['nextcloudcalendar_version'], '0.1.0', '>=');
    }

    public static function depends_on(): array
    {
        return ['\phpbb\db\migration\data\v320\v320'];
    }

    public function update_schema(): array
    {
        return [
            'add_tables' => [
                $this->table_prefix . 'nextcloudcalendar_events' => [
                    'COLUMNS' => [
                        'event_id' => ['UINT', null, 'auto_increment'],
                        'user_id' => ['UINT', 0],
                        'username' => ['VCHAR:255', ''],
                        'title' => ['VCHAR:255', ''],
                        'description' => ['MTEXT_UNI', ''],
                        'location' => ['VCHAR:255', ''],
                        'start_time' => ['TIMESTAMP', 0],
                        'end_time' => ['TIMESTAMP', 0],
                        'status' => ['VCHAR:20', 'pending'],
                        'created_time' => ['TIMESTAMP', 0],
                        'approved_time' => ['TIMESTAMP', 0],
                        'approved_user_id' => ['UINT', 0],
                        'nextcloud_uid' => ['VCHAR:255', ''],
                        'nextcloud_error' => ['TEXT_UNI', ''],
                    ],
                    'PRIMARY_KEY' => 'event_id',
                    'KEYS' => [
                        'status' => ['INDEX', 'status'],
                        'start_time' => ['INDEX', 'start_time'],
                        'user_id' => ['INDEX', 'user_id'],
                    ],
                ],
            ],
        ];
    }

    public function revert_schema(): array
    {
        return [
            'drop_tables' => [
                $this->table_prefix . 'nextcloudcalendar_events',
            ],
        ];
    }

    public function update_data(): array
    {
        return [
            ['config.add', ['nextcloudcalendar_version', '0.1.0']],
            ['config.add', ['nextcloudcalendar_enabled', 1]],
            ['config.add', ['nextcloudcalendar_calendar_url', '']],
            ['config.add', ['nextcloudcalendar_username', '']],
            ['config.add', ['nextcloudcalendar_timezone', 'Europe/Berlin']],

            ['permission.add', ['u_nextcloudcalendar_create']],
            ['permission.add', ['m_nextcloudcalendar_approve', true]],
            ['permission.add', ['a_nextcloudcalendar']],

            ['permission.permission_set', ['REGISTERED', 'u_nextcloudcalendar_create', 'group']],
            ['permission.permission_set', ['GLOBAL_MODERATORS', 'm_nextcloudcalendar_approve', 'group']],
            ['permission.permission_set', ['ADMINISTRATORS', 'a_nextcloudcalendar', 'group']],

            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_NEXTCLOUDCALENDAR_TITLE',
            ]],
            ['module.add', [
                'acp',
                'ACP_NEXTCLOUDCALENDAR_TITLE',
                [
                    'module_basename' => '\maxbrenne\nextcloudcalendar\acp\main_module',
                    'modes' => ['settings'],
                ],
            ]],
            ['module.add', [
                'mcp',
                'MCP_MAIN',
                [
                    'module_basename' => '\maxbrenne\nextcloudcalendar\mcp\main_module',
                    'modes' => ['queue'],
                ],
            ]],
        ];
    }
}
