<?php

namespace maxbrenne\nextcloudcalendar\acp;

class main_module
{
    private const FRONTEND_POSITIONS = ['none', 'navigation', 'index', 'footer'];

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

        if ($request->is_set_post('submit') || $request->is_set_post('test_connection'))
        {
            if (!check_form_key('acp_nextcloudcalendar_settings'))
            {
                $errors[] = $user->lang('FORM_INVALID');
            }
            else
            {
                $config->set('nextcloudcalendar_enabled', $request->variable('enabled', 0));
                $config->set('nextcloudcalendar_calendar_url', trim($request->variable('calendar_url', '', true)));
                $config->set('nextcloudcalendar_username', trim($request->variable('username', '', true)));
                $config->set('nextcloudcalendar_timezone', trim($request->variable('timezone', 'Europe/Berlin', true)));
                $frontend_position = $request->variable('frontend_position', 'navigation');
                $config->set('nextcloudcalendar_frontend_position', in_array($frontend_position, self::FRONTEND_POSITIONS, true) ? $frontend_position : 'navigation');
                $password = $request->variable('password', '', true);

                if ($password !== '')
                {
                    $config_text->set('nextcloudcalendar_password', $password);
                }

                if ($request->is_set_post('test_connection'))
                {
                    $test_result = $caldav->test_connection() ? $user->lang('ACP_NEXTCLOUDCALENDAR_TEST_OK') : $caldav->get_last_error();
                }
                else
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
            'ENABLED' => (int) $config['nextcloudcalendar_enabled'],
            'CALENDAR_URL' => $config['nextcloudcalendar_calendar_url'],
            'NEXTCLOUDCALENDAR_USERNAME' => $config['nextcloudcalendar_username'],
            'TIMEZONE' => $config['nextcloudcalendar_timezone'],
            'FRONTEND_POSITION_NONE' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'none',
            'FRONTEND_POSITION_NAVIGATION' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'navigation',
            'FRONTEND_POSITION_INDEX' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'index',
            'FRONTEND_POSITION_FOOTER' => ($config['nextcloudcalendar_frontend_position'] ?? 'navigation') === 'footer',
        ]);
    }
}
