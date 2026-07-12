<?php

namespace maxbrenne\nextcloudcalendar\migrations;

class v_0_1_9 extends \phpbb\db\migration\migration
{
    public function effectively_installed(): bool
    {
        return isset($this->config['nextcloudcalendar_version']) && version_compare($this->config['nextcloudcalendar_version'], '0.1.9', '>=');
    }

    public static function depends_on(): array
    {
        return ['\maxbrenne\nextcloudcalendar\migrations\v_0_1_8'];
    }

    public function update_data(): array
    {
        return [
            ['config.add', ['nextcloudcalendar_frontend_icon', 'fa-calendar-plus-o']],
            ['custom', [[$this, 'rename_legacy_index_position']]],
            ['config.update', ['nextcloudcalendar_version', '0.1.9']],
        ];
    }

    public function rename_legacy_index_position(): void
    {
        if (($this->config['nextcloudcalendar_frontend_position'] ?? '') === 'index')
        {
            $this->config->set('nextcloudcalendar_frontend_position', 'index_button');
        }
    }
}
