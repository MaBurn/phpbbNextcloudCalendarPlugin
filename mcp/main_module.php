<?php

namespace maxbrenne\nextcloudcalendar\mcp;

class main_module
{
    public string $u_action;
    public string $tpl_name;
    public string $page_title;

    public function main($id, $mode): void
    {
        global $db, $phpbb_container, $request, $template, $user;

        $user->add_lang_ext('maxbrenne/nextcloudcalendar', 'common');
        $user->add_lang_ext('maxbrenne/nextcloudcalendar', 'mcp');

        $this->tpl_name = 'mcp_nextcloudcalendar_queue';
        $this->page_title = $user->lang('MCP_NEXTCLOUDCALENDAR_QUEUE');

        $events_table = $phpbb_container->getParameter('core.table_prefix') . 'nextcloudcalendar_events';
        $caldav = $phpbb_container->get('maxbrenne.nextcloudcalendar.service.caldav');
        add_form_key('mcp_nextcloudcalendar_queue');

        $message = '';
        $message_is_error = false;

        if ($request->is_set_post('approve') || $request->is_set_post('reject'))
        {
            if (!check_form_key('mcp_nextcloudcalendar_queue'))
            {
                $message = $user->lang('FORM_INVALID');
                $message_is_error = true;
            }
            else
            {
                $event_id = $request->variable('event_id', 0);
                $event = $this->load_event($events_table, $event_id);

                if ($event)
                {
                    if ($request->is_set_post('approve'))
                    {
                        // Claim the event first so two moderators cannot both push it to Nextcloud.
                        $sql_ary = [
                            'status' => 'approved',
                            'approved_time' => time(),
                            'approved_user_id' => (int) $user->data['user_id'],
                        ];
                        $db->sql_query('UPDATE ' . $events_table . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE event_id = " . (int) $event_id . " AND status = 'pending'");

                        if (!$db->sql_affectedrows())
                        {
                            $message = $user->lang('MCP_NEXTCLOUDCALENDAR_ALREADY_HANDLED');
                            $message_is_error = true;
                        }
                        else
                        {
                            $uid = $caldav->create_event($event);

                            if ($uid !== null)
                            {
                                $sql_ary = ['nextcloud_uid' => $uid, 'nextcloud_error' => ''];
                                $db->sql_query('UPDATE ' . $events_table . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' WHERE event_id = ' . (int) $event_id);
                                $message = $user->lang('MCP_NEXTCLOUDCALENDAR_APPROVED');
                            }
                            else
                            {
                                // CalDAV failed: release the claim so the event stays in the queue.
                                $sql_ary = [
                                    'status' => 'pending',
                                    'approved_time' => 0,
                                    'approved_user_id' => 0,
                                    'nextcloud_error' => $caldav->get_last_error(),
                                ];
                                $db->sql_query('UPDATE ' . $events_table . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' WHERE event_id = ' . (int) $event_id);
                                $message = $caldav->get_last_error();
                                $message_is_error = true;
                            }
                        }
                    }
                    else
                    {
                        $sql_ary = [
                            'status' => 'rejected',
                            'approved_time' => time(),
                            'approved_user_id' => (int) $user->data['user_id'],
                        ];
                        $db->sql_query('UPDATE ' . $events_table . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE event_id = " . (int) $event_id . " AND status = 'pending'");

                        if ($db->sql_affectedrows())
                        {
                            $message = $user->lang('MCP_NEXTCLOUDCALENDAR_REJECTED');
                        }
                        else
                        {
                            $message = $user->lang('MCP_NEXTCLOUDCALENDAR_ALREADY_HANDLED');
                            $message_is_error = true;
                        }
                    }
                }
            }
        }

        $sql = 'SELECT *
            FROM ' . $events_table . "
            WHERE status = 'pending'
            ORDER BY start_time ASC";
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            $template->assign_block_vars('events', [
                'EVENT_ID' => (int) $row['event_id'],
                'TITLE' => $row['title'],
                'DESCRIPTION' => $row['description'],
                'LOCATION' => $row['location'],
                'USERNAME' => $row['username'],
                'START' => $user->format_date((int) $row['start_time']),
                'END' => $user->format_date((int) $row['end_time']),
                'CREATED' => $user->format_date((int) $row['created_time']),
                'NEXTCLOUD_ERROR' => $row['nextcloud_error'],
            ]);
        }
        $db->sql_freeresult($result);

        $template->assign_vars([
            'U_ACTION' => $this->u_action,
            'MESSAGE' => $message,
            'S_MESSAGE_ERROR' => $message_is_error,
        ]);
    }

    protected function load_event(string $events_table, int $event_id): ?array
    {
        global $db;

        $sql = 'SELECT *
            FROM ' . $events_table . "
            WHERE event_id = $event_id
                AND status = 'pending'";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        return $row ?: null;
    }
}
