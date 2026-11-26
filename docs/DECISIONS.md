# Decisions

These decisions are binding for every task. Change one only with Tobi's agreement, and record the change here with the date and the reason.

## 1. Technical decisions

| Topic | Decision |
|---|---|
| Framework | The current stable Laravel release that the PHP version on the hosting supports. Task R0-01 records both versions here. |
| Database | MySQL on staging and live. SQLite on the developer's Mac and in the tests, so that nothing has to be installed. Migrations must run on both. |
| Front end | Blade templates. One stylesheet, `public/css/ern.css`, made from `docs/mockup/theme.css` and `docs/mockup/app.css`. Small scripts in `public/js/` without a framework. No npm, no Vite, no Tailwind. Remove the framework's Node scaffolding. |
| Works without scripts | Every page and every form works with JavaScript switched off. Scripts only add comfort (menus, filters without reload, copy buttons). |
| Login | Laravel Fortify with our own Blade views: sign-in, sign-out, password reset, e-mail verification. Public registration is switched off, because membership goes through the application in release 1. If Fortify has been superseded, use the framework's current equivalent and record it here. |
| Roles | One user can hold several roles: member, reviewer, final_editor, conference_team, database_lead, lecture_team, editor, admin. Checks go through policies and gates. Admin holds every right. |
| Tests | PHPUnit, as shipped with the framework. Feature tests for every page and every action, including one test per permission check that a wrong role is refused. |
| Code style | Laravel Pint with its default rules. |
| Repository | A private GitHub repository. The main branch is protected: changes arrive only through pull requests, and a pull request can only be merged when the automatic test run is green. |
| Quality gate | `composer check` runs Pint in test mode and all tests. It runs in the session before every commit, and again on GitHub for every pull request (GitHub Actions). |
| Content | Pages and news are stored as Markdown and rendered with the framework's own Markdown converter, with raw HTML switched off. |
| Search | Behind one interface, `App\Search\PublicationSearch`. On MySQL it uses FULLTEXT indexes. On SQLite it uses a simple LIKE fallback, which is enough for tests. |
| Scheduled jobs | The framework's scheduler, triggered by one cron entry at the hosting. No queue workers. E-mails are sent directly or through the database queue that the scheduler processes. |
| E-mail | The hosting's SMTP mailbox on staging and live. The `log` mailer locally. Every e-mail is a class with a plain-text version and a test. |
| Files | Uploads go to private storage and are served through a controller that checks permissions. |
| Times | Stored in UTC. Shown in the time zone of the event, with the zone named. |
| Third parties | No trackers, no external fonts, no external scripts. Videos are links or load only after a click. Only the session cookie is set, so that no cookie banner is needed. Tobi has this confirmed with the privacy policy. |
| Accessibility | Keyboard use, visible focus, labels on every field, 200% zoom without loss, respect for reduced motion. |
| Language | The interface is in English. Texts live in the templates. No translation layer for now. |
| Uploading | Through GitHub Actions, started by hand with a button: one workflow for staging, one for live, one to go back to the previous version. The server's access details are GitHub secrets. Claude sessions have no access to the server. |
| Feeds | RSS for news, new database entries and saved searches. An .ics feed for events and deadlines. Public content only. |
| Ernie | Last release. Do not build, prepare or stub it earlier. |

Versions recorded by R0-01:

- PHP in the development environment: 8.3.6 (Claude Code in the browser, 26 November 2026). Composer 2.8.12, git 2.43.0. PHP, Composer and git come pre-installed there, with the extensions Laravel needs (including pdo_sqlite and pdo_mysql).
- PHP on the hosting: (the Laravel release current in November 2026, version 13, needs PHP 8.3 or newer)
- Laravel:

## 2. Address plan

The mockup uses `#` addresses. The platform uses these:

| Mockup | Platform |
|---|---|
| `#home` | `/` |
| `#news` | `/news`, one item `/news/{slug}` |
| `#conferences` | `/conferences` |
| `#ernc27`, `#ernc26`, older | `/conferences/{slug}` with the tabs as `/conferences/{slug}/{tab}` |
| abstract view | `/conferences/{slug}/abstracts/{id}` |
| `#submission` | `/submissions/{token}` |
| `#database` | `/database`, publication `/database/{id}`, lists `/database/lists` |
| `#saved` | `/saved` |
| `#live` | `/live` |
| `#lectures` | `/teaching`, unit `/teaching/{slug}`, lecture of the series `/teaching/series/{n}` |
| `#course` | `/teaching/guide` |
| `#propose`, `#mylectures` | `/teaching/propose`, `/teaching/my-lectures` |
| `#podcast` | `/podcast` |
| `#events` | `/events`, feed `/events.ics` |
| `#about`, `#involved`, `#join` | `/about`, `/get-involved`, `/join` |
| `#conduct`, `#concern`, `#imprint` | `/code-of-conduct`, `/report-a-concern`, `/imprint` |
| `#members` | `/members`, directory `/members/directory`, researcher `/members/{slug}`, profile `/members/profile` |
| `#admin` | `/admin`, with the tabs as `/admin/{tab}` |
| `#guide` | not built. It explains the mockup. |
| feeds | `/feed/news.xml`, `/feed/database.xml` |

## 3. Decided during the mockup phase

These came from Tobi and the board's reviewers. They are not open for reinterpretation.

- Membership is free. Applicants are not promised a time for approval.
- Abstracts are submitted without an account. The author gets a private link to their submission.
- Reviewers give feedback in a text field. One final editor, a single person, gives the final result back to the author.
- The lecture series is a concept that any institution can use. No university is named as its owner on the pages.
- Lecture recordings are used in full. Chapters are time marks only. Nothing is cut.
- Two ERNC26 talks must never show a recording, a transcript or a teaching unit, because the presenters asked not to be uploaded: "Being an Esports Fan Across Borders" and "Equality Work in Esports". Their abstracts stay, as in the published Book of Abstracts. This is final.
- The member area is personal and about ERN. New articles and press are for members only.
- The start page has no "for members" block.
- Discord: the platform stores a member's Discord name. There is no Discord channel view. A bot of our own is left for later.
- The concern form goes to the Ombudsperson only.
- Candidates of board elections are never shown on the platform.
- The mockup's "Guide" page, the mock bar at the top and all "nothing is sent" notices belong to the mockup only.
