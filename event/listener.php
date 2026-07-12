<?php

namespace maxbrenne\nextcloudcalendar\event;

use phpbb\auth\auth;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use maxbrenne\nextcloudcalendar\service\form_renderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    protected auth $auth;
    protected form_renderer $form_renderer;
    protected helper $helper;
    protected template $template;
    protected user $user;

    public function __construct(auth $auth, helper $helper, form_renderer $form_renderer, template $template, user $user)
    {
        $this->auth = $auth;
        $this->helper = $helper;
        $this->form_renderer = $form_renderer;
        $this->template = $template;
        $this->user = $user;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'core.page_header' => 'add_calendar_link',
            'core.modify_text_for_display_after' => 'render_shortcode',
        ];
    }

    public function add_calendar_link(): void
    {
        $this->user->add_lang_ext('maxbrenne/nextcloudcalendar', 'common');

        $this->template->assign_vars([
            'S_NEXTCLOUDCALENDAR_CAN_CREATE' => $this->auth->acl_get('u_nextcloudcalendar_create'),
            'U_NEXTCLOUDCALENDAR_REQUEST' => $this->helper->route('maxbrenne_nextcloudcalendar_request'),
        ]);
    }

    public function render_shortcode($event): void
    {
        if (strpos($event['text'], '[nextcloudcalendar]') === false)
        {
            return;
        }

        $event['text'] = str_replace('[nextcloudcalendar]', $this->form_renderer->render(), $event['text']);
    }
}
