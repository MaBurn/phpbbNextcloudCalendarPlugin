<?php

namespace maxbrenne\nextcloudcalendar\migrations;

class v_0_1_8 extends \phpbb\db\migration\migration
{
    public function effectively_installed(): bool
    {
        return isset($this->config['nextcloudcalendar_version']) && version_compare($this->config['nextcloudcalendar_version'], '0.1.8', '>=');
    }

    public static function depends_on(): array
    {
        return ['\maxbrenne\nextcloudcalendar\migrations\install'];
    }

    public function update_data(): array
    {
        return [
            ['config.add', ['nextcloudcalendar_frontend_position', 'navigation']],
            ['config.update', ['nextcloudcalendar_version', '0.1.8']],
        ];
    }
}
