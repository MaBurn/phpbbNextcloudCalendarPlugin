# Architektur-Dokumentation — Nextcloud Calendar für phpBB

Diese Dokumentation beschreibt den Anwendungsfall, den Ablauf und den Aufbau der
phpBB-Erweiterung `maxbrenne/nextcloudcalendar`. Die Diagramme sind in
[Mermaid](https://mermaid.js.org/) notiert und werden von GitHub, GitLab und
VS Code (mit Mermaid-Unterstützung) direkt gerendert.

## 1. Anwendungsfall (Use Case)

Die Erweiterung ermöglicht **moderierte Terminvorschläge**: Forenmitglieder
schlagen Termine vor, Moderatoren prüfen sie, und erst nach Freigabe landet der
Termin in einem gemeinsamen Nextcloud-Kalender.

```mermaid
flowchart LR
    subgraph Akteure
        U(["👤 Registrierter Benutzer"])
        M(["🛡️ Moderator"])
        A(["⚙️ Administrator"])
    end

    subgraph phpBB["phpBB-Forum mit Erweiterung"]
        UC1["Termin vorschlagen<br>(Formular)"]
        UC2["Vorschläge prüfen<br>(MCP-Warteschlange)"]
        UC3["Freigeben / Ablehnen"]
        UC4["Einstellungen verwalten<br>(ACP)"]
        UC5["Verbindung testen"]
    end

    NC[("☁️ Nextcloud<br>CalDAV-Kalender")]

    U -->|u_nextcloudcalendar_create| UC1
    M -->|m_nextcloudcalendar_approve| UC2
    M --> UC3
    A -->|a_nextcloudcalendar| UC4
    A --> UC5
    UC3 -->|"CalDAV PUT (nur bei Freigabe)"| NC
    UC5 -->|CalDAV PROPFIND| NC
```

**Berechtigungen** (werden bei der Installation angelegt und zugewiesen):

| Berechtigung | Standard-Gruppe | Zweck |
|---|---|---|
| `u_nextcloudcalendar_create` | Registrierte Benutzer | Termine vorschlagen |
| `m_nextcloudcalendar_approve` | Globale Moderatoren | Vorschläge freigeben/ablehnen |
| `a_nextcloudcalendar` | Administratoren | ACP-Einstellungen verwalten |

## 2. Ablauf (End-to-End)

### 2.1 Einreichung und Freigabe

```mermaid
sequenceDiagram
    autonumber
    actor U as Benutzer
    participant C as request_controller
    participant DB as phpbb_nextcloudcalendar_events
    actor M as Moderator
    participant MCP as mcp/main_module
    participant CAL as caldav_client
    participant NC as Nextcloud (CalDAV)

    U->>C: GET /nextcloudcalendar/request
    C-->>U: Formular (Titel, Beschreibung, Ort, Start, Ende)
    U->>C: POST (submit)
    C->>C: Form-Key prüfen (CSRF)
    C->>C: Eingaben validieren<br>(Titel, striktes Datumsformat, Ende > Start)
    C->>DB: INSERT status='pending'
    C-->>U: „Vorschlag eingereicht, wartet auf Freigabe“

    M->>MCP: MCP-Warteschlange öffnen
    MCP->>DB: SELECT WHERE status='pending' ORDER BY start_time
    MCP-->>M: Liste wartender Vorschläge

    alt Freigeben
        M->>MCP: POST approve
        MCP->>DB: UPDATE status='approved'<br>WHERE status='pending' (Claim)
        alt Claim erfolgreich
            MCP->>CAL: create_event(event)
            CAL->>CAL: UID erzeugen, ICS bauen<br>(Entities dekodieren, RFC-5545-Folding)
            CAL->>NC: PUT <kalender-url>/<uid>.ics
            alt HTTP 2xx
                NC-->>CAL: 201 Created
                MCP->>DB: UPDATE nextcloud_uid
                MCP-->>M: „Freigegeben und eingetragen“
            else Fehler
                NC-->>CAL: z. B. 401 / Timeout
                MCP->>DB: UPDATE status='pending',<br>nextcloud_error
                MCP-->>M: Fehlermeldung (bleibt in Queue)
            end
        else Bereits bearbeitet
            MCP-->>M: „Bereits von anderem Moderator bearbeitet“
        end
    else Ablehnen
        M->>MCP: POST reject
        MCP->>DB: UPDATE status='rejected'<br>WHERE status='pending'
        MCP-->>M: „Abgelehnt“
    end
```

### 2.2 Statusmodell eines Vorschlags

```mermaid
stateDiagram-v2
    [*] --> pending: Benutzer reicht ein
    pending --> approved: Moderator gibt frei (Claim)
    approved --> pending: CalDAV-PUT fehlgeschlagen<br>(Fehler wird gespeichert)
    approved --> [*]: Nextcloud-UID gespeichert
    pending --> rejected: Moderator lehnt ab
    rejected --> [*]
```

Wichtig: Die Freigabe „claimt“ den Datensatz zuerst per bedingtem `UPDATE …
WHERE status = 'pending'`. Nur wer den Claim gewinnt, schreibt nach Nextcloud —
so entstehen keine doppelten Kalendereinträge, wenn zwei Moderatoren
gleichzeitig freigeben. Schlägt der CalDAV-PUT fehl, wird der Claim wieder
freigegeben und der Fehler in `nextcloud_error` angezeigt.

### 2.3 Verbindungstest (ACP)

```mermaid
sequenceDiagram
    autonumber
    actor A as Administrator
    participant ACP as acp/main_module
    participant CAL as caldav_client
    participant NC as Nextcloud

    A->>ACP: „Verbindung testen“
    ACP->>ACP: Einstellungen speichern<br>(inkl. Zeitzonen-Validierung)
    ACP->>CAL: test_connection()
    CAL->>NC: PROPFIND (Depth 0, Basic Auth)
    alt HTTP 2xx
        ACP-->>A: ✅ „Verbindung erfolgreich“
    else Fehler
        CAL->>CAL: Diagnose (falsche URL-Art,<br>User/Kalender-Mismatch bei 401, …)
        ACP-->>A: ❌ Fehlermeldung
    end
```

Hinweis: Der Button „Verbindung testen“ **speichert** die eingegebenen
Einstellungen ebenfalls, damit der Test die aktuellen Werte verwendet.

## 3. Software-Aufbau

### 3.1 Komponenten

```mermaid
flowchart TB
    subgraph Core["phpBB 3.3 Core"]
        EV["Event-System<br>core.page_header<br>core.modify_text_for_display_after<br>core.modify_module_row"]
        RT["Symfony-Routing"]
        MOD["Modul-System (ACP/MCP)"]
        DBAL[("DBAL")]
        CFG[("config / config_text")]
    end

    subgraph Ext["Erweiterung maxbrenne/nextcloudcalendar"]
        L["event/listener.php<br><i>Navigation, Shortcode,<br>Modul-Übersetzung</i>"]
        RC["controller/request_controller.php<br><i>Formular anzeigen,<br>Einreichung validieren + speichern</i>"]
        FR["service/form_renderer.php<br><i>Formular-Template rendern,<br>Rechte/Enabled prüfen</i>"]
        IH["service/icon_helper.php<br><i>FA-Icon-Klasse normalisieren</i>"]
        CC["service/caldav_client.php<br><i>PROPFIND / PUT, ICS-Erzeugung</i>"]
        ACPM["acp/main_module.php<br><i>Einstellungen + Verbindungstest</i>"]
        MCPM["mcp/main_module.php<br><i>Warteschlange, Freigabe/Ablehnung</i>"]
        MIG["migrations/<br><i>Schema, Config, Rechte, Module</i>"]
        TPL["styles/ + adm/style/<br><i>Templates + CSS</i>"]
    end

    NC[("☁️ Nextcloud<br>CalDAV")]
    T[("phpbb_nextcloudcalendar_events")]

    EV --> L
    RT -->|/nextcloudcalendar/request| RC
    MOD --> ACPM
    MOD --> MCPM

    L --> FR
    L --> IH
    RC --> FR
    ACPM --> IH
    ACPM --> CC
    MCPM --> CC

    RC --> DBAL
    MCPM --> DBAL
    DBAL --- T
    CC --> CFG
    CC ==>|HTTPS Basic Auth| NC
    MIG -.->|erzeugt| T
```

Alle Dienste sind in [config/services.yml](../config/services.yml) registriert;
die Route steht in [config/routing.yml](../config/routing.yml). Die ACP-/MCP-
Module werden über `acp/main_info.php` bzw. `mcp/main_info.php` beim
Modul-System angemeldet und über die Migration installiert.

### 3.2 Einstiegspunkte

| Einstieg | Mechanismus | Code |
|---|---|---|
| Frontend-Link (Navigation, Quicklinks, Index-Button/-Kachel, Footer) | Event `core.page_header` + Template-Events | [event/listener.php](../event/listener.php), `styles/all/template/event/` |
| Formularseite `/app.php/nextcloudcalendar/request` | Symfony-Route | [controller/request_controller.php](../controller/request_controller.php) |
| Shortcode `[nextcloudcalendar]` in Beiträgen | Event `core.modify_text_for_display_after` | [event/listener.php](../event/listener.php) → [service/form_renderer.php](../service/form_renderer.php) |
| ACP-Einstellungen | phpBB-Modul (Kategorie „MODS“) | [acp/main_module.php](../acp/main_module.php) |
| MCP-Warteschlange | phpBB-Modul (MCP_MAIN) | [mcp/main_module.php](../mcp/main_module.php) |

### 3.3 Datenmodell

```mermaid
erDiagram
    PHPBB_USERS ||--o{ NEXTCLOUDCALENDAR_EVENTS : "reicht ein (user_id)"
    PHPBB_USERS ||--o{ NEXTCLOUDCALENDAR_EVENTS : "bearbeitet (approved_user_id)"

    NEXTCLOUDCALENDAR_EVENTS {
        uint event_id PK "auto_increment"
        uint user_id "Einreicher"
        varchar_255 username "Anzeigename (Snapshot)"
        varchar_255 title "Termintitel"
        mtext description "Beschreibung"
        varchar_255 location "Ort"
        timestamp start_time "Beginn (Unix, UTC)"
        timestamp end_time "Ende (Unix, UTC)"
        varchar_20 status "pending | approved | rejected"
        timestamp created_time "Einreichzeitpunkt"
        timestamp approved_time "Bearbeitungszeitpunkt"
        uint approved_user_id "Bearbeitender Moderator"
        varchar_255 nextcloud_uid "iCalendar-UID in Nextcloud"
        text nextcloud_error "Letzter CalDAV-Fehler"
    }
```

Indizes: `status`, `start_time`, `user_id`. Start/Ende werden bei der Eingabe
in der konfigurierten Zeitzone interpretiert und als UTC-Unix-Timestamps
gespeichert; die ICS-Datei verwendet UTC (`…Z`).

### 3.4 Konfiguration

| Schlüssel | Ablage | Bedeutung |
|---|---|---|
| `nextcloudcalendar_enabled` | `config` | Einreichungen an/aus |
| `nextcloudcalendar_calendar_url` | `config` | CalDAV-Kalender-URL |
| `nextcloudcalendar_username` | `config` | Technischer Nextcloud-Benutzer |
| `nextcloudcalendar_password` | `config_text` | App-Passwort (Klartext, s. u.) |
| `nextcloudcalendar_timezone` | `config` | Zeitzone für Eingaben (validiert) |
| `nextcloudcalendar_frontend_position` | `config` | Position des Frontend-Links |
| `nextcloudcalendar_frontend_icon` | `config` | FontAwesome-Icon-Klasse |
| `nextcloudcalendar_version` | `config` | Migrationsstand |

### 3.5 Verzeichnisstruktur

```text
nextcloudcalendar/
├── ext.php                     # Extension-Basisklasse
├── composer.json               # Metadaten (type: phpbb-extension)
├── config/
│   ├── routing.yml             # Route /nextcloudcalendar/request
│   └── services.yml            # DI-Container-Definitionen
├── controller/
│   └── request_controller.php  # Einreichungsformular (GET/POST)
├── event/
│   └── listener.php            # Core-Event-Subscriber
├── service/
│   ├── caldav_client.php       # CalDAV-HTTP-Client + ICS-Builder
│   ├── form_renderer.php       # Formular-Rendering (Seite + Shortcode)
│   └── icon_helper.php         # Icon-Normalisierung (geteilt)
├── acp/                        # Admin-Modul (Einstellungen)
├── mcp/                        # Moderations-Modul (Warteschlange)
├── migrations/                 # install.php, v_0_1_8.php, v_0_1_9.php
├── language/{de,en,fr}/        # Sprachdateien
├── adm/style/                  # ACP-Template
└── styles/all/                 # Frontend-Templates, Template-Events, CSS
```

## 4. Sicherheit & Designentscheidungen

- **CSRF**: Alle Formulare (Einreichung, ACP, MCP) nutzen phpBB-Form-Keys
  (`add_form_key` / `check_form_key`).
- **SQL**: Werte laufen durch `sql_build_array`, IDs werden nach `int` gecastet.
- **XSS**: phpBB escapt Request-Eingaben bereits beim Einlesen
  (`htmlspecialchars`); für die ICS-Ausgabe werden die Entities gezielt wieder
  dekodiert und anschließend RFC-5545-konform escaped und gefaltet.
- **Doppel-Freigabe**: Bedingtes UPDATE („Claim“) verhindert doppelte
  Nextcloud-Einträge bei parallelen Moderatoren (siehe 2.2).
- **Passwort-Ablage**: Das App-Passwort liegt als Klartext in `config_text`,
  weil CalDAV-Basic-Auth das Klartext-Passwort für jeden Request benötigt.
  Empfehlung: dediziertes Nextcloud-App-Passwort mit minimalen Rechten
  verwenden, nicht das Konto-Passwort.
- **Timeouts**: cURL mit `CONNECTTIMEOUT 10 s` / `TIMEOUT 20 s`, damit ein
  nicht erreichbarer Nextcloud-Host weder ACP noch MCP blockiert.
- **Fehlertoleranz**: Ein fehlgeschlagener CalDAV-PUT verwirft den Vorschlag
  nicht — er bleibt mit sichtbarer Fehlermeldung in der Warteschlange.

## 5. Bekannte Grenzen

- Keine Bearbeitung oder Löschung bereits freigegebener Termine aus phpBB
  heraus (kein CalDAV-DELETE/Update).
- Keine Pagination in der MCP-Warteschlange (bei sehr vielen offenen
  Vorschlägen wird die Seite lang).
- Der Shortcode `[nextcloudcalendar]` wird überall gerendert, wo Beitragstext
  angezeigt wird (auch Signaturen/PNs); Berechtigung und Aktiv-Schalter werden
  dabei aber stets geprüft.
- Ganztägige oder wiederkehrende Termine (RRULE) werden nicht unterstützt.
