<?php

namespace maxbrenne\nextcloudcalendar\controller;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\request\request_interface;
use maxbrenne\nextcloudcalendar\service\form_renderer;
use phpbb\user;
use Symfony\Component\HttpFoundation\Response;

class request_controller
{
    protected auth $auth;
    protected config $config;
    protected driver_interface $db;
    protected form_renderer $form_renderer;
    protected helper $helper;
    protected request_interface $request;
    protected user $user;
    protected string $events_table;

    public function __construct(auth $auth, config $config, driver_interface $db, helper $helper, request_interface $request, form_renderer $form_renderer, user $user, string $table_prefix)
    {
        $this->auth = $auth;
        $this->config = $config;
        $this->db = $db;
        $this->form_renderer = $form_renderer;
        $this->helper = $helper;
        $this->request = $request;
        $this->user = $user;
        $this->events_table = $table_prefix . 'nextcloudcalendar_events';
    }

    public function handle(): Response
    {
        $this->user->add_lang_ext('maxbrenne/nextcloudcalendar', 'common');

        if (!$this->auth->acl_get('u_nextcloudcalendar_create'))
        {
            trigger_error($this->user->lang('NEXTCLOUDCALENDAR_NOT_AUTHORISED'));
        }

        if (empty($this->config['nextcloudcalendar_enabled']))
        {
            trigger_error($this->user->lang('NEXTCLOUDCALENDAR_DISABLED'));
        }

        $error = '';
        $data = [
            'title' => $this->request->variable('event_title', '', true),
            'description' => $this->request->variable('event_description', '', true),
            'location' => $this->request->variable('event_location', '', true),
            'start' => $this->request->variable('event_start', ''),
            'end' => $this->request->variable('event_end', ''),
        ];

        if ($this->request->is_set_post('submit'))
        {
            if (!check_form_key('nextcloudcalendar_request'))
            {
                $error = $this->user->lang('FORM_INVALID');
            }
            else
            {
                $timestamps = $this->parse_times($data['start'], $data['end']);

                if (trim($data['title']) === '' || !$timestamps)
                {
                    $error = $this->user->lang('NEXTCLOUDCALENDAR_FORM_INVALID');
                }
                else if ($timestamps['end'] <= $timestamps['start'])
                {
                    $error = $this->user->lang('NEXTCLOUDCALENDAR_END_BEFORE_START');
                }
                else
                {
                    $sql_ary = [
                        'user_id' => (int) $this->user->data['user_id'],
                        'username' => $this->user->data['username'],
                        'title' => $data['title'],
                        'description' => $data['description'],
                        'location' => $data['location'],
                        'start_time' => $timestamps['start'],
                        'end_time' => $timestamps['end'],
                        'status' => 'pending',
                        'created_time' => time(),
                        'approved_time' => 0,
                        'approved_user_id' => 0,
                        'nextcloud_uid' => '',
                        'nextcloud_error' => '',
                    ];

                    $this->db->sql_query('INSERT INTO ' . $this->events_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
                    trigger_error($this->user->lang('NEXTCLOUDCALENDAR_SUBMITTED'));
                }
            }
        }

        $this->form_renderer->assign_vars($data, $error);

        return $this->helper->render('calendar_request.html', $this->user->lang('NEXTCLOUDCALENDAR_PAGE_TITLE'));
    }

    protected function parse_times(string $start, string $end): ?array
    {
        $timezone_name = $this->config['nextcloudcalendar_timezone'] ?: ($this->config['board_timezone'] ?: 'UTC');

        try
        {
            $timezone = new \DateTimeZone($timezone_name);
            $start_date = new \DateTimeImmutable($start, $timezone);
            $end_date = new \DateTimeImmutable($end, $timezone);
        }
        catch (\Exception $e)
        {
            return null;
        }

        return [
            'start' => $start_date->getTimestamp(),
            'end' => $end_date->getTimestamp(),
        ];
    }
}
