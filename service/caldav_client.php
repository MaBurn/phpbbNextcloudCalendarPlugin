<?php

namespace maxbrenne\nextcloudcalendar\service;

use phpbb\config\config;
use phpbb\config\db_text;

class caldav_client
{
    protected config $config;
    protected db_text $config_text;
    protected string $last_error = '';

    public function __construct(config $config, db_text $config_text)
    {
        $this->config = $config;
        $this->config_text = $config_text;
    }

    public function get_last_error(): string
    {
        return $this->last_error;
    }

    public function test_connection(): bool
    {
        $response = $this->request('PROPFIND', $this->calendar_url(), '', [
            'Depth: 0',
            'Content-Type: application/xml; charset=utf-8',
        ]);

        return $response['ok'];
    }

    public function create_event(array $event): ?string
    {
        $uid = $this->generate_uid((int) $event['event_id']);
        $url = $this->calendar_url() . rawurlencode($uid) . '.ics';
        $ics = $this->build_ics($uid, $event);

        $response = $this->request('PUT', $url, $ics, [
            'Content-Type: text/calendar; charset=utf-8',
        ]);

        return $response['ok'] ? $uid : null;
    }

    protected function request(string $method, string $url, string $body, array $headers): array
    {
        $this->last_error = '';

        if (!function_exists('curl_init'))
        {
            $this->last_error = 'PHP cURL extension is not available.';
            return ['ok' => false, 'status' => 0];
        }

        if ($url === '' || $this->config['nextcloudcalendar_username'] === '' || $this->password() === '')
        {
            $this->last_error = 'Nextcloud calendar URL, user or password is missing.';
            return ['ok' => false, 'status' => 0];
        }

        if (strpos($url, '/apps/calendar/p/') !== false)
        {
            $this->last_error = 'The configured Nextcloud URL is a public calendar share, not a CalDAV URL. Use the private CalDAV URL from Nextcloud calendar settings.';
            return ['ok' => false, 'status' => 0];
        }

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERPWD => $this->config['nextcloudcalendar_username'] . ':' . $this->password(),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 20,
        ]);

        if ($body !== '')
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $result = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        if ($result === false)
        {
            $this->last_error = curl_error($curl);
            curl_close($curl);
            return ['ok' => false, 'status' => $status];
        }

        curl_close($curl);

        $ok = $status >= 200 && $status < 300;
        if (!$ok)
        {
            $this->last_error = 'Nextcloud returned HTTP ' . $status . '.';

            if ($status === 401 && preg_match('#/remote\.php/dav/calendars/([^/]+)/#', $url, $matches) === 1)
            {
                $calendar_user = rawurldecode($matches[1]);
                $configured_user = (string) $this->config['nextcloudcalendar_username'];

                if ($calendar_user !== $configured_user)
                {
                    $this->last_error .= ' The CalDAV URL belongs to "' . $calendar_user . '", but the configured technical user is "' . $configured_user . '".';
                }
            }
        }

        return ['ok' => $ok, 'status' => $status];
    }

    protected function calendar_url(): string
    {
        $url = trim((string) $this->config['nextcloudcalendar_calendar_url']);

        return $url === '' ? '' : rtrim($url, '/') . '/';
    }

    protected function password(): string
    {
        return (string) $this->config_text->get('nextcloudcalendar_password');
    }

    protected function generate_uid(int $event_id): string
    {
        $host = parse_url($this->calendar_url(), PHP_URL_HOST) ?: 'phpbb';

        return 'phpbb-nextcloudcalendar-' . $event_id . '-' . bin2hex(random_bytes(8)) . '@' . $host;
    }

    protected function build_ics(string $uid, array $event): string
    {
        $created = gmdate('Ymd\THis\Z', (int) $event['created_time']);
        $start = gmdate('Ymd\THis\Z', (int) $event['start_time']);
        $end = gmdate('Ymd\THis\Z', (int) $event['end_time']);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//phpBB Nextcloud Calendar//EN',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:' . $this->escape_text($uid),
            'DTSTAMP:' . gmdate('Ymd\THis\Z'),
            'CREATED:' . $created,
            'DTSTART:' . $start,
            'DTEND:' . $end,
            'SUMMARY:' . $this->escape_text((string) $event['title']),
            'DESCRIPTION:' . $this->escape_text((string) $event['description']),
            'LOCATION:' . $this->escape_text((string) $event['location']),
            'END:VEVENT',
            'END:VCALENDAR',
            '',
        ];

        return implode("\r\n", $lines);
    }

    protected function escape_text(string $value): string
    {
        $value = str_replace(["\\", "\r\n", "\r", "\n", ';', ','], ['\\\\', '\n', '\n', '\n', '\;', '\,'], $value);

        return $value;
    }
}
