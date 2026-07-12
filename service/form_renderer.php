<?php

namespace maxbrenne\nextcloudcalendar\service;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;

class form_renderer
{
    protected auth $auth;
    protected config $config;
    protected helper $helper;
    protected template $template;
    protected user $user;

    public function __construct(auth $auth, config $config, helper $helper, template $template, user $user)
    {
        $this->auth = $auth;
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
    }

    public function render(array $data = [], string $error = ''): string
    {
        $this->assign_vars($data, $error);

        $this->template->set_filenames([
            'nextcloudcalendar_form' => '@maxbrenne_nextcloudcalendar/calendar_request_form.html',
        ]);

        return $this->template->assign_display('nextcloudcalendar_form', '', true);
    }

    public function assign_vars(array $data = [], string $error = ''): void
    {
        $this->user->add_lang_ext('maxbrenne/nextcloudcalendar', 'common');

        if (empty($this->config['nextcloudcalendar_enabled']))
        {
            $this->template->assign_vars([
                'S_NEXTCLOUDCALENDAR_UNAVAILABLE' => true,
                'NEXTCLOUDCALENDAR_UNAVAILABLE_MSG' => $this->user->lang('NEXTCLOUDCALENDAR_DISABLED'),
            ]);
            return;
        }

        if (!$this->auth->acl_get('u_nextcloudcalendar_create'))
        {
            $this->template->assign_vars([
                'S_NEXTCLOUDCALENDAR_UNAVAILABLE' => true,
                'NEXTCLOUDCALENDAR_UNAVAILABLE_MSG' => $this->user->lang('NEXTCLOUDCALENDAR_NOT_AUTHORISED'),
            ]);
            return;
        }

        add_form_key('nextcloudcalendar_request');

        $this->template->assign_vars([
            'S_ERROR' => $error !== '',
            'ERROR_MSG' => $error,
            'U_ACTION' => $this->helper->route('maxbrenne_nextcloudcalendar_request'),
            'EVENT_TITLE' => $data['title'] ?? '',
            'EVENT_DESCRIPTION' => $data['description'] ?? '',
            'EVENT_LOCATION' => $data['location'] ?? '',
            'EVENT_START' => $data['start'] ?? '',
            'EVENT_END' => $data['end'] ?? '',
            'S_NEXTCLOUDCALENDAR_UNAVAILABLE' => false,
        ]);
    }
}
