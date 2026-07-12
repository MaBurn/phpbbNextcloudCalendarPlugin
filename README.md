# Nextcloud Calendar for phpBB

Nextcloud Calendar is a phpBB 3.3 extension for moderated calendar submissions.
Registered users can suggest events in phpBB, moderators can approve or reject
them in the Moderator Control Panel, and approved events are written to a
Nextcloud calendar through CalDAV.

## Features

- Adds a configurable "Suggest calendar event" frontend link for users with
  permission.
- Provides a public submission form with title, description, location, start and
  end time.
- Stores submissions in a phpBB database table until they are reviewed.
- Adds an MCP queue where moderators can approve or reject pending events.
- Creates approved events in Nextcloud as iCalendar `VEVENT` entries via CalDAV.
- Adds an ACP settings page for enabling submissions, configuring CalDAV
  credentials and testing the connection.
- Supports English, German and French language files.
- Supports embedding the submission form in phpBB content with
  `[nextcloudcalendar]`.

## Requirements

- phpBB `>=3.3.0,<4.0.0`
- PHP `>=7.4`
- PHP cURL extension
- A Nextcloud calendar CalDAV URL
- A technical Nextcloud user with write access to the target calendar

## Installation

Install the extension into phpBB under the exact extension path:

```text
ext/maxbrenne/nextcloudcalendar/
```

If you use the release ZIP, extract the ZIP directly into phpBB's `ext`
directory. The ZIP already contains the `maxbrenne/nextcloudcalendar` path:

```text
phpBB/
└── ext/
    └── maxbrenne/
        └── nextcloudcalendar/
            ├── composer.json
            └── ext.php
```

If you copy the repository contents manually:

1. Create the directory `ext/maxbrenne/nextcloudcalendar` in your phpBB
   installation.
2. Copy this repository's contents into that directory.
3. Make sure `composer.json` and `ext.php` are directly inside
   `ext/maxbrenne/nextcloudcalendar/`, not in another nested
   `maxbrenne/nextcloudcalendar` directory.
4. In the phpBB ACP, open **Customise > Manage extensions**.
5. Enable **Nextcloud Calendar**.

The extension name in `composer.json` is `maxbrenne/nextcloudcalendar`, so phpBB
expects the `maxbrenne/nextcloudcalendar` directory layout.

## Configuration

Open **ACP > Extensions > Nextcloud calendar > Settings** and configure:

- **Enable calendar submissions**: turns the submission form on or off.
- **Show frontend link**: chooses where phpBB shows the submission link outside
  posts. Available positions are main navigation, board index above the forum
  list, footer, or no automatic link. Embedded forms in posts using
  `[nextcloudcalendar]` are not affected.
- **CalDAV calendar URL**: the full target calendar URL, for example
  `https://cloud.example.org/remote.php/dav/calendars/calendar-user/community-calendar/`.
- **Technical user**: the Nextcloud user used for CalDAV writes.
- **App password or password**: use a Nextcloud app password when possible.
- **Timezone**: timezone used to interpret submitted local date/time values.

Use **Test connection** to verify that phpBB can reach the configured calendar.

## Permissions

The migration adds these phpBB permissions:

- `u_nextcloudcalendar_create`: users can submit calendar suggestions.
- `m_nextcloudcalendar_approve`: moderators can approve or reject submissions.
- `a_nextcloudcalendar`: administrators can manage the extension settings.

Default assignments:

- Registered users can submit suggestions.
- Global moderators can approve suggestions.
- Administrators can manage settings.

## Workflow

1. A user submits an event proposal.
2. The proposal is stored as `pending` in `phpbb_nextcloudcalendar_events`.
3. A moderator opens the MCP queue.
4. On approval, the extension writes the event to Nextcloud using CalDAV `PUT`.
5. On success, the local proposal is marked as `approved` and the Nextcloud UID
   is stored.
6. On failure, the proposal remains pending and the last CalDAV error is shown
   in the MCP queue.

## Review Status

The extension source follows the standard phpBB extension layout:

- `composer.json` declares `type: phpbb-extension`.
- `ext.php` defines the extension class.
- `config/services.yml` registers controller, services and event listener.
- `config/routing.yml` registers the submission route.
- `migrations/install.php` creates configuration, permissions, modules and the
  event table.
- `acp/`, `mcp/`, `language/`, `styles/` and `adm/style/` are present in the
  expected phpBB extension locations.

The repository intentionally excludes generated ZIP files, `.DS_Store` files,
Composer vendor files and `composer.lock`.

## Verification Notes

Static review found no blocking structural issue for activation in phpBB when
installed under `ext/maxbrenne/nextcloudcalendar`.

These checks passed in a Podman container using the `composer:2` image on
2026-07-13:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
composer validate
```

`composer validate --strict` reports a warning for the `version` field. The
field is intentionally kept because phpBB 3.3.11 requires it when validating an
extension for display in the ACP extension manager.

Then enable the extension in a phpBB 3.3 test installation and verify:

- ACP settings page opens.
- Connection test succeeds against the target Nextcloud calendar.
- A user can submit an event.
- A moderator can approve it.
- The event appears in Nextcloud.

## License

GPL-2.0-only
