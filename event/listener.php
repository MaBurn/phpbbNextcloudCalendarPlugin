<?php

namespace maxbrenne\nextcloudcalendar\event;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use maxbrenne\nextcloudcalendar\service\form_renderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    protected auth $auth;
    protected config $config;
    protected form_renderer $form_renderer;
    protected helper $helper;
    protected template $template;
    protected user $user;

    public function __construct(auth $auth, config $config, helper $helper, form_renderer $form_renderer, template $template, user $user)
    {
        $this->auth = $auth;
        $this->config = $config;
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
            'core.modify_module_row' => 'translate_module_row',
        ];
    }

    public function add_calendar_link(): void
    {
        $this->user->add_lang_ext('maxbrenne/nextcloudcalendar', 'common');

        $can_create = !empty($this->config['nextcloudcalendar_enabled']) && $this->auth->acl_get('u_nextcloudcalendar_create');
        $frontend_position = $this->config['nextcloudcalendar_frontend_position'] ?? 'navigation';
        $frontend_icon = $this->normalise_icon($this->config['nextcloudcalendar_frontend_icon'] ?? 'fa-calendar-plus-o');

        $this->template->assign_vars([
            'S_NEXTCLOUDCALENDAR_CAN_CREATE' => $can_create,
            'S_NEXTCLOUDCALENDAR_SHOW_NAVIGATION' => $can_create && $frontend_position === 'navigation',
            'S_NEXTCLOUDCALENDAR_SHOW_QUICKLINKS' => $can_create && $frontend_position === 'quicklinks',
            'S_NEXTCLOUDCALENDAR_SHOW_INDEX_BUTTON' => $can_create && $frontend_position === 'index_button',
            'S_NEXTCLOUDCALENDAR_SHOW_INDEX_TILE' => $can_create && $frontend_position === 'index_tile',
            'S_NEXTCLOUDCALENDAR_SHOW_FOOTER' => $can_create && $frontend_position === 'footer',
            'NEXTCLOUDCALENDAR_FRONTEND_ICON' => $frontend_icon,
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

    public function translate_module_row($event): void
    {
        $module_row = $event['module_row'];
        $langname = $module_row['langname'] ?? '';

        if (strpos($langname, 'ACP_NEXTCLOUDCALENDAR_') !== 0 && strpos($langname, 'MCP_NEXTCLOUDCALENDAR_') !== 0)
        {
            return;
        }

        $this->user->add_lang_ext('maxbrenne/nextcloudcalendar', 'acp');
        $this->user->add_lang_ext('maxbrenne/nextcloudcalendar', 'mcp');

        $translated = $this->user->lang($langname);
        if ($translated === $langname)
        {
            $translated = $this->fallback_module_label($langname);
        }

        $module_row['lang'] = $translated;
        $event['module_row'] = $module_row;
    }

    protected function fallback_module_label(string $langname): string
    {
        $is_german = strpos($this->user->lang_name, 'de') === 0;
        $is_french = strpos($this->user->lang_name, 'fr') === 0;

        $labels = [
            'ACP_NEXTCLOUDCALENDAR_TITLE' => ['Nextcloud calendar', 'Nextcloud-Kalender', 'Calendrier Nextcloud'],
            'ACP_NEXTCLOUDCALENDAR_SETTINGS' => ['Settings', 'Einstellungen', 'Parametres'],
            'MCP_NEXTCLOUDCALENDAR_TITLE' => ['Nextcloud calendar', 'Nextcloud-Kalender', 'Calendrier Nextcloud'],
            'MCP_NEXTCLOUDCALENDAR_QUEUE' => ['Approve calendar events', 'Kalendereinträge freigeben', 'Valider les evenements'],
        ];

        if (!isset($labels[$langname]))
        {
            return $langname;
        }

        return $labels[$langname][$is_french ? 2 : ($is_german ? 1 : 0)];
    }

    protected function normalise_icon(string $icon): string
    {
        $icon = trim($icon);
        $icon = preg_replace('/[^a-z0-9\-\s]/i', '', $icon);
        $parts = preg_split('/\s+/', (string) $icon, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part)
        {
            if (strpos($part, 'fa-') === 0)
            {
                return $part;
            }
        }

        if (!empty($parts[0]) && $parts[0] !== 'fa')
        {
            return 'fa-' . $parts[0];
        }

        return 'fa-calendar-plus-o';
    }
}
