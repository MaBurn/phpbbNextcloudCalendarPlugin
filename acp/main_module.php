<?php

namespace maxbrenne\nextcloudcalendar\acp;

use maxbrenne\nextcloudcalendar\service\icon_helper;

class main_module
{
    private const FRONTEND_POSITIONS = ['none', 'navigation', 'quicklinks', 'index_button', 'index_tile', 'footer'];

    public string $u_action;
    public string $tpl_name;
    public string $page_title;

    public function main($id, $mode): void
    {
        global $config, $phpbb_container, $request, $template, $user;

        $user->add_lang_ext('maxbrenne/nextcloudcalendar', 'acp');

        $this->tpl_name = 'acp_nextcloudcalendar_settings';
        $this->page_title = $user->lang('ACP_NEXTCLOUDCALENDAR_SETTINGS');

        $config_text = $phpbb_container->get('config_text');
        $caldav = $phpbb_container->get('maxbrenne.nextcloudcalendar.service.caldav');
        add_form_key('acp_nextcloudcalendar_settings');

        $errors = [];
        $test_result = '';
        $test_ok = false;

        if ($request->is_set_post('submit') || $request->is_set_post('test_connection'))
        {
            if (!check_form_key('acp_nextcloudcalendar_settings'))
            {
                $errors[] = $user->lang('FORM_INVALID');
            }
            else
            {
                $timezone = trim($request->variable('timezone', 'Europe/Berlin', true));

                try
                {
                    new \DateTimeZone($timezone);
                }
                catch (\Exception $e)
                {
                    $errors[] = $user->lang('ACP_NEXTCLOUDCALENDAR_TIMEZONE_INVALID', $timezone);
                    $timezone = $config['nextcloudcalendar_timezone'];
                }

                $config->set('nextcloudcalendar_enabled', $request->variable('enabled', 0));
                $config->set('nextcloudcalendar_calendar_url', trim($request->variable('calendar_url', '', true)));
                $config->set('nextcloudcalendar_username', trim($request->variable('username', '', true)));
                $config->set('nextcloudcalendar_timezone', $timezone);
                $frontend_position = $request->variable('frontend_position', 'navigation');
                $config->set('nextcloudcalendar_frontend_position', in_array($frontend_position, self::FRONTEND_POSITIONS, true) ? $frontend_position : 'navigation');
                $config->set('nextcloudcalendar_frontend_icon', icon_helper::normalise($request->variable('frontend_icon', icon_helper::DEFAULT_ICON, true)));
                $password = $request->variable('password', '', true);

                if ($password !== '')
                {
                    $config_text->set('nextcloudcalendar_password', $password);
                }

                if ($request->is_set_post('test_connection'))
                {
                    $test_ok = $caldav->test_connection();
                    $test_result = $test_ok ? $user->lang('ACP_NEXTCLOUDCALENDAR_TEST_OK') : $caldav->get_last_error();
                }
                else if (empty($errors))
                {
                    trigger_error($user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
                }
            }
        }

        $template->assign_vars([
            'U_ACTION' => $this->u_action,
            'S_ERROR' => !empty($errors),
            'ERROR_MSG' => implode('<br>', $errors),
            'TEST_RESULT' => $test_result,
            'S_TEST_OK' => $test_ok,
            'ENABLED' => (int) $config['nextcloudcalendar_enabled'],
            'CALENDAR_URL' => $config['nextcloudcalendar_calendar_url'],
            'NEXTCLOUDCALENDAR_USERNAME' => $config['nextcloudcalendar_username'],
            'TIMEZONE' => $config['nextcloudcalendar_timezone'],
            'FRONTEND_POSITION_NONE' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'none',
            'FRONTEND_POSITION_NAVIGATION' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'navigation',
            'FRONTEND_POSITION_QUICKLINKS' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'quicklinks',
            'FRONTEND_POSITION_INDEX_BUTTON' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'index_button',
            'FRONTEND_POSITION_INDEX_TILE' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'index_tile',
            'FRONTEND_POSITION_FOOTER' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'footer',
            'FRONTEND_ICON' => $config['nextcloudcalendar_frontend_icon'] ?? icon_helper::DEFAULT_ICON,
        ]);
    }
}
