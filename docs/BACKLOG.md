# Backlog

All tasks, in the order they are built. This file is the single source of truth for what is done.

Marks: `[ ]` open, `[x]` done, `[H]` waits for Tobi, `[!]` blocked. The rules for working through it are in `CLAUDE.md`.

Each task names:

- **Model:** `sonnet` (plan it yourself) or `opus` (the `planner` agent plans it).
- **Needs:** tasks that must be `[x]` first. A task whose needs are open is skipped until they are done.
- **Can:** what someone can do afterwards.
- **Mockup:** where to look in `docs/mockup/`.
- **Test:** what must be proven by tests.

Releases 0 and 1 are detailed. Releases 2 to 7 are outlines. Before a release starts, `/plan-release <n>` turns its outline into detailed tasks in this file.

---

## Release 0: Foundations

### [x] R0-01 Check the environment (2026-11-26)
- Model: sonnet
- Can: Tobi knows that the environment is ready.
- Do: Check that `php`, `composer` and `git` are available and note their versions. Check that Composer can reach the package registry (`composer show laravel/laravel --available` or similar). If something is missing or blocked, stop and tell Tobi in plain words what to change in the environment's settings, or that this session should move to the desktop app. If PHP and Composer have to be installed at the start of every session, add a setup script and a session-start hook in `.claude/settings.json` that install them and run `composer install`. Record the PHP version in `docs/DECISIONS.md`.
- Test: none. The commands answer.

### [x] R0-02 Create the application (2026-11-26)
- Model: sonnet
- Needs: R0-01
- Can: A developer opens the start page of an empty application on the Mac.
- Do: Create the Laravel application in this folder without overwriting `CLAUDE.md`, `README.md`, `docs/` or `.claude/` (create it in a temporary folder and move the files in). Use SQLite locally. Remove all Node scaffolding: `package.json`, Vite configuration, the Vite directive in templates. Set the application name to "Esports Research Network". Add `.env.example` for local use and `.env.staging.example` with empty values for the server. Record the Laravel version in `docs/DECISIONS.md`. Make sure `.gitignore` covers `.env`, `vendor/` and the SQLite file.
- Test: The shipped example tests pass. `php artisan serve` shows a page.

### [x] R0-03 Quality gate (2026-11-26)
- Model: sonnet
- Needs: R0-02
- Can: One command says whether the code is fit to commit.
- Do: Add the Composer script `check` that runs Pint in test mode and then all tests. Add the script `fix` that runs Pint. Make the tests use an in-memory SQLite database. Add the GitHub Actions workflow `.github/workflows/check.yml` that runs `composer check` on every pull request, on the PHP version recorded in `docs/DECISIONS.md`. In the hand-over note, tell Tobi in three plain steps how to protect the main branch on GitHub so that a pull request needs a green check.
- Test: `composer check` passes. The workflow file is valid.

### [x] R0-04 Styles and page frame (2026-11-26)
- Model: sonnet
- Needs: R0-03
- Can: A visitor sees the ERN header, navigation, mobile menu and footer on every page, in the ERN look.
- Mockup: `shell.html`, `theme.css`, `app.css`, `img/`, `js/10-home.js` (for the placeholder)
- Do: Build `public/css/ern.css` from `theme.css` followed by `app.css`. Copy the images to `public/img/`. Build the layout template: skip link, sticky header with logo, navigation (News, Conferences, Database, Live, Teaching, Podcast, About Us, Get Involved), the saved-library icon, the member button, the mobile menu, the footer with its three columns and social links. Leave out the mock bar and the Ernie button. The mobile menu must open without JavaScript too. Add a placeholder start page and a 404 page in the same frame. Mark the current navigation item.
- Test: The start page answers 200 and contains every navigation label. An unknown address answers 404 inside the frame.

### [x] R0-05 Sign-in (2026-11-26)
- Model: opus
- Needs: R0-04
- Can: A member signs in, signs out, and resets a forgotten password by e-mail.
- Mockup: `js/60-members.js` (sign-in, reset and activation modes)
- Do: Install Laravel Fortify. Switch off public registration. Build our own views for sign-in, forgotten password, new password. Extend the users table: first name, last name, slug, institution, country, status (career stage), interests, ORCID, Discord name, directory visibility, approved_at. Limit sign-in attempts. Wording from the mockup, including the neutral answer after a reset request that does not reveal whether an address exists.
- Test: Sign-in with right and wrong password. Lock after repeated failures. Reset e-mail is sent and the link sets a new password. A user who is not approved cannot sign in. The reset request answers the same for known and unknown addresses.

### [x] R0-06 Roles and permissions (2026-11-26)
- Model: opus
- Needs: R0-05
- Can: The platform knows who may do what.
- Mockup: `js/65-admin.js` (rights editor: the list of rights and their descriptions)
- Do: Roles as in `docs/DECISIONS.md`, many per user, in a pivot table. One gate per area: manage members, manage conference, review submissions, decide submissions, manage database, manage teaching, edit content, administer. Admin passes every gate. A middleware for the admin area. A helper for templates.
- Test: For every gate: a user with the role passes, a member without it is refused with 403, a guest is sent to sign-in.

### [x] R0-07 Test data (2026-11-26)
- Model: sonnet
- Needs: R0-06
- Can: A developer gets a filled application with one command.
- Mockup: `data.json` (`people`, `me`)
- Do: Factories and seeders. One test user per role with obvious names and the address pattern `<role>@example.test`. Twenty invented members across countries and career stages, using the mockup's example people first. All institutions of invented people carry "(example)". The passwords of test users come from a local-only value in `.env.example`. Seeders refuse to run when the environment is `production`.
- Test: The seeder runs on an empty database. It refuses in production.

### [x] R0-08 Admin frame (2026-11-26)
- Model: sonnet
- Needs: R0-07
- Can: A person with any admin role opens the admin area and sees only the tabs their roles allow.
- Mockup: `js/65-admin.js` (tabs Overview, Members, Conference, Database, Teaching, Content)
- Do: `/admin` with the tab bar and an empty page per tab. Add a "System" page for admins that shows the application version, the environment name and the time of the last scheduled run. Add a scheduled heartbeat job that stores that time. Link "Admin area" in the footer for users with an admin role only.
- Test: Each tab is refused for a member and allowed for the fitting role. The heartbeat command stores a time.

### [H] R0-09 Prepare the hosting
- Needs: R0-02
- Tobi: See `docs/HUMAN_TODO.md`, R0-09. When Tobi reaches this task, explain each step in plain words and wait. You cannot check the server yourself. The check happens through the first upload in R0-10. Tobi reads the hosting's PHP version in the customer area and tells you. Record it in `docs/DECISIONS.md`. If it is too old for the installed Laravel version, stop and say so.

### [ ] R0-10 Upload workflows
- Model: opus
- Needs: R0-08, R0-09
- Can: Tobi uploads the current main branch to staging with one button on GitHub and can go back with one button.
- Do: Three GitHub Actions workflows that are started by hand only (`workflow_dispatch`): `deploy-staging.yml`, `deploy-live.yml`, `rollback.yml`. An upload checks out main, runs `composer check`, installs the Composer packages without development packages inside the workflow, uploads the result over SSH as a new dated folder under `releases/`, links the shared `.env` and `storage`, runs the migrations, switches the `current` link, and keeps the last five releases. The rollback switches `current` to the previous release. Host, user, path and key come from GitHub secrets, one set per environment, and never appear in the log. The live workflow asks for the word "live" as confirmation and uses a protected GitHub environment that needs Tobi's approval. The staging site sends a "noindex" header. The live workflow first runs `php artisan ern:check-launch` on the server's database and stops if it fails (added by R1-05: no going live while the privacy policy is the placeholder). Write `docs/DEPLOY.md` in plain words, with pictures described in words: where the button is, what green and red mean, how to go back. Never start an upload yourself.
- Test: The workflow files are valid. The upload steps live in one shell script in `scripts/` that has a dry-run mode, and a test runs it in dry-run mode.

### [H] R0-11 Hosting test
- Needs: R0-10
- Tobi: See `docs/HUMAN_TODO.md`, R0-11. Before he tests: add an admin-only button on the "System" page that sends a test e-mail to the signed-in admin, add a workflow step that creates the first admin on staging from the test data, and give Tobi the one cron line to enter at the hosting. He starts the first upload with the button. This task decides whether the hosting carries the platform.

### [ ] R0-12 Backups
- Model: sonnet
- Needs: R0-11
- Can: The database is saved every night and a backup can be restored.
- Do: A scheduled command that dumps the database into private storage with the date in the name and removes dumps older than 30 days. A restore command that refuses to run on live without an explicit confirmation. Describe both in `docs/DEPLOY.md`.
- Test: The dump command writes a file and prunes old ones.

### [H] R0-13 Architecture review
- Needs: R0-08
- Tobi: Optional. Switch to Fable 5.2 and run `/review-architecture`. It reads the decisions, the backlog and the mockup, proposes the data model for releases 1 to 6 as `docs/DATA_MODEL.md`, and lists risks. It changes no code. If Tobi skips this, mark it `[x] skipped` and let the `planner` write `docs/DATA_MODEL.md` as part of `/plan-release`.

---

## Release 1: Site and members

### [x] R1-01 Pages and news in the database (2026-11-26)
- Model: sonnet
- Needs: R0-08
- Can: An editor writes and changes pages and news in the admin area without code.
- Mockup: `js/65-admin.js` (Content tab), `data.json` (`news`, `pages`)
- Do: Models for pages (slug, title, body in Markdown) and news (title, slug, summary, body, image, published_at, draft or published). Editor screens in the Content tab with preview. Seed the content from `data.json`.
- Test: An editor creates, changes and publishes. A member is refused. Drafts are invisible to the public. Raw HTML in the body is not rendered.

### [x] R1-02 Start page (2026-11-26)
- Model: sonnet
- Needs: R1-01
- Mockup: `js/10-home.js`, `data.json` (`home`)
- Can: A visitor sees the hero, the three starting points, the featured event, "Coming up", the latest news, the mission and the ways to get involved.
- Test: The page shows the three latest published news items and no drafts.

### [x] R1-03 News (2026-11-26)
- Model: sonnet
- Needs: R1-01
- Mockup: `js/10-home.js` (news list)
- Can: A visitor reads the news list and single news items. A feed reader subscribes to `/feed/news.xml`.
- Test: List with pages, single item, 404 for a draft, a valid RSS feed with the ten latest items.

### [x] R1-04 About Us and Get Involved (2026-11-26)
- Model: sonnet
- Needs: R1-01
- Mockup: `js/50-about.js`, `data.json` (`about`, `goals`, `involved`), `img/members.svg`
- Can: A visitor reads who ERN is, the goals, the board, the member map and the ways to take part.
- Do: The board members are content that an editor can change. No election candidates anywhere.
- Test: Pages answer 200 with the seeded content.

### [x] R1-05 Code of Conduct, Imprint, Privacy (2026-11-26)
- Model: sonnet
- Needs: R1-01
- Mockup: `js/50-about.js`, `data.json` (`conduct`, `imprint`)
- Can: A visitor reads the Code of Conduct, the imprint and the privacy policy.
- Do: Three editable pages. The privacy page carries a clear placeholder until Tobi delivers the text (see `docs/HUMAN_TODO.md`). Add a check to the live upload that refuses while the placeholder is in place.
- Test: Pages answer. The live upload check refuses with the placeholder.

### [x] R1-06 Conferences overview (2026-11-26)
- Model: sonnet
- Needs: R1-01
- Mockup: `js/20-conf.js` (overview, summaries of past conferences, ERNC27 Call tab), `data.json` (`confs`, `ernc27`)
- Can: A visitor sees all conferences, the summary of each past one, and the ERNC27 call with its dates.
- Do: Model for conferences (slug, name, place, dates, host, summary, status, key dates). The button "Submit an abstract" for ERNC27 links to the current submission form on the old site, through a configurable address. The full conference pages come in release 4.
- Test: Overview and each conference page answer. A conference team member edits the dates, a member is refused.

### [x] R1-07 Podcast and Live (2026-11-26)
- Model: sonnet
- Needs: R1-01
- Mockup: `js/40-live.js`, `data.json` (`podcast`, `liveDay`)
- Can: A visitor finds the podcast with its episodes and platforms, and the live page with the stream link and what is on.
- Do: The stream and the videos are links or load only after a click. No external scripts on page load.
- Test: Pages answer and contain no external script or frame in their first response.

### [x] R1-08 Events and deadlines (2026-11-26)
- Model: sonnet
- Needs: R1-06
- Mockup: `js/45-events.js`
- Can: A visitor sees upcoming events and deadlines in one list and subscribes to them in a calendar through `/events.ics`.
- Do: Model for events (title, kind, start, end, time zone, place, link). Conference key dates appear automatically. Editors add other events in the Content tab.
- Test: The list is ordered and hides past entries. The .ics feed is valid and carries the time zones.

### [ ] R1-09 Join ERN
- Model: opus
- Needs: R0-07
- Mockup: `js/50-about.js` (`join`, the facts and the pending timeline)
- Can: A researcher applies for membership online and gets a confirmation by e-mail.
- Do: Model for applications, separate from users. Fields and wording from the mockup. Agreement to the Code of Conduct and the privacy policy is required and stored with its time. An address that already belongs to a member or an open application gets the same friendly confirmation and no second application, so that nobody can test who is a member. Protection against spam without a third-party service: a hidden field and a rate limit. No approval time is promised.
- Test: Valid application is stored and confirmed by e-mail. Every required field is enforced. A duplicate creates nothing and answers the same. The rate limit works.

### [ ] R1-10 Approving members
- Model: opus
- Needs: R1-09
- Mockup: `js/65-admin.js` (Members tab: applications, decision, history)
- Can: A person with the right to manage members sees open applications, approves or declines them, and the applicant gets the fitting e-mail.
- Do: Approval creates the user with the role member and sends a welcome e-mail with a signed, expiring link to set a password. Declining sends the mockup's wording and keeps only what the privacy policy allows: delete the application's personal data after 30 days by a scheduled job. Every decision is logged with who and when.
- Test: Approve, decline, expiry of the link, the deletion job, refusal for a user without the right.

### [ ] R1-11 Activation
- Model: opus
- Needs: R1-10
- Mockup: `js/60-members.js` (activate mode)
- Can: A new member sets a password through the link in the welcome e-mail and lands in the member area. An expired link offers a new one.
- Test: Valid link, expired link, used link, weak password refused.

### [ ] R1-12 Profile
- Model: opus
- Needs: R1-11
- Mockup: `js/60-members.js` (profile)
- Can: A member updates their own details, including the Discord name, and decides what the directory shows.
- Do: Changing the e-mail address needs confirmation through the new address. A member can ask to end the membership: this sends a request to the people who manage members and deletes nothing by itself. A member can download their own data as a file.
- Test: A member changes only their own profile. Another member's profile is refused. The e-mail change needs confirmation.

### [ ] R1-13 Directory and researcher pages
- Model: opus
- Needs: R1-12
- Mockup: `js/60-members.js` (directory filters, `researcher`)
- Can: A signed-in member searches the directory by name, country, career stage and topic, and opens a researcher's page.
- Do: Only for signed-in members. Only members who chose to be listed appear, and only the fields they released. E-mail addresses are never shown. Contact goes through a form that sends an e-mail and does not reveal the address.
- Test: A guest is sent to sign-in. A hidden member appears nowhere, not in search, not by direct address. Released and withheld fields. The contact form has a rate limit.

### [ ] R1-14 Member overview
- Model: sonnet
- Needs: R1-12
- Mockup: `js/60-members.js` (member overview)
- Can: A member sees a personal start page: their profile status, ERN notices, next events, and their roles.
- Do: The blocks for saved items, new articles and press come with releases 2 and 6. Leave them out until then. Do not show empty shells.
- Test: The page shows the member's own data and the published notices.

### [ ] R1-15 Members list and rights editor
- Model: opus
- Needs: R1-10
- Mockup: `js/65-admin.js` (Members tab: list, rights editor)
- Can: A person who manages members searches the member list and opens a member. An admin changes a member's roles.
- Do: Only admins change roles. Nobody removes their own admin role, and the last admin cannot be removed. Every role change is logged. Export of the member list as a spreadsheet file for people who manage members, logged as well.
- Test: Role change by admin, refusal for others, the two safeguards, the log entries.

### [ ] R1-16 Admin overview
- Model: sonnet
- Needs: R1-15
- Mockup: `js/65-admin.js` (Overview, "Needs attention")
- Can: A person with an admin role sees at a glance what waits for them: open applications, drafts, and later the other queues.
- Test: The counts match the data and show only what the person's roles cover.

### [H] R1-17 Report a Concern
- Model: opus
- Needs: R1-05
- Tobi: See `docs/HUMAN_TODO.md`, R1-17. Build only after he confirms with `/done R1-17` that the Ombudsperson agrees and the address is in the server's `.env`.
- Mockup: `js/50-about.js` (`concern`), `data.json` (`concern`)
- Can: Anyone reports a concern. It reaches the Ombudsperson by e-mail and nobody else.
- Do: The form sends one e-mail to the configured address and stores nothing: no database row, no log line with content, no copy to admins. Anonymous reports are possible. If sending fails, the person is told clearly and nothing is kept. Wording from the mockup.
- Test: The e-mail goes to the configured address only. The database and the log hold no content afterwards. A send failure is reported to the person.

### [ ] R1-18 Old addresses
- Model: sonnet
- Needs: R1-08
- Can: Every address of the old site leads to the right new page.
- Do: A redirect table in a configuration file, filled from the page list in `docs/old-site-addresses.txt`, which Tobi or a later task supplies. Until then, cover the known ones: `/about`, `/conference`, `/get-involved`, `/news`, the concern form, the imprint. Permanent redirects.
- Test: Each listed address redirects once to a page that answers 200.

### [H] R1-19 Member import
- Model: opus
- Needs: R1-15
- Tobi: See `docs/HUMAN_TODO.md`, R1-19.
- Can: The current members are in the platform without anyone typing them in.
- Do: A command that reads a spreadsheet export (CSV) with a documented column layout, creates approved members without passwords, reports duplicates and unreadable rows by row number only, without any personal data in its output, and has a dry-run option. A workflow started by hand runs the command on the server against a file that Tobi has placed there himself, first as a dry run. The file never passes through GitHub and is deleted from the server after a successful import. Members then set a password through "Forgotten password" or through an invitation e-mail that a second command sends in batches. Build and test it with an invented file only. The real file is handled by Tobi on the server.
- Test: Import of an invented file, duplicates, broken rows, dry run, the invitation batches.

### [ ] R1-20 Accessibility and phone pass
- Model: sonnet
- Needs: R1-16
- Can: Every page of release 1 works on a phone, with the keyboard only, at 200% zoom, and with JavaScript switched off.
- Do: Go through every template of releases 0 and 1. One h1 per page, labels on every field, errors tied to their fields, visible focus, sensible page titles, no horizontal scrolling at 360 pixels width. Write what you could only check by reading the code into the hand-over note, so that the human check covers it.
- Test: A test that every public address answers 200 and has exactly one h1 and a title.

### [ ] R1-21 Release notes and check sheet
- Model: sonnet
- Needs: R1-20
- Can: A person who did not build the release can check it.
- Do: Write `docs/checks/release-1.md`: twenty concrete tasks for a visitor, an applicant, a member and an admin, each with the expected result and the mockup page to compare with. Plain words, no technical terms.

### [H] R1-22 Release check on staging
- Needs: R1-21
- Tobi: Start the upload to staging. A person who did not build it works through `docs/checks/release-1.md`. Findings go back to Claude as new tasks at the end of this release.

### [H] R1-23 Cutover
- Model: opus
- Needs: R1-22, R1-19, R1-17, R1-05
- Tobi: After the ERNC27 decisions are out. Claude writes `docs/CUTOVER.md` with the steps for the day, the test of every redirect, the way back, and the archive of the old site. Tobi carries it out.

---

## Release 2: Database (outline)

Run `/plan-release 2` before starting. Mockup: `js/30-db.js`, `js/35-saved.js`, `js/65-admin.js` (Database tab), `data.json` (`pubs`).

- Publication model and search interface, with provenance ("About this entry")
- One-time import from papers.esportsresearch.net, tested with an invented export
- Search page with filters, sorting and pages
- Publication pages
- Saved library for members
- Reading lists: curated by the Database Lead, readable by all
- Saved searches for members
- "Report a problem with this entry", into the Database Lead's queue
- Daily job that finds new articles through public interfaces (Crossref, OpenAlex, PubMed) and puts them into a queue. Nothing is published without approval. Classification by Haiku 5.5 through the API, switched off unless a key is configured.
- Database tab in the admin area: queue, approve, reject, edit, merge duplicates
- RSS feeds for new entries and for each saved search
- Check sheet and release check `[H]`

## Release 3: Teaching library (outline)

Run `/plan-release 3`. Mockup: `js/27-talk.js`, `js/28-zip.js`, `js/65-admin.js` (Teaching tab).

- Teaching unit model: recording link, length, chapters as time marks, outcomes, discussion questions, reflection prompts, 45-minute plan, permissions of the speaker
- Teaching library page and unit pages, with student view and teacher guide
- Course guide: how to run the series as a course, the essay, credit conversions as rough guides
- Download of a unit as a package
- Autumn series: programme as draft and published, lecture pages
- Propose a lecture, without account, with a private link
- "My lectures" for speakers
- Teaching tab: proposals, the eight workflow steps, feedback to speakers, unit editor
- The two ERNC26 talks named in `docs/DECISIONS.md` never get a unit
- Check sheet and release check `[H]`

## Release 4: Conference pages (outline)

Run `/plan-release 4`. Mockup: `js/20-conf.js`, `js/25-pdf.js`, `js/27-talk.js` (transcripts).

- Sessions, slots and rooms. Programme by day
- Import of the accepted ERNC27 abstracts from the old site's export, tested with an invented file
- Abstract pages. Books of Abstracts as pages and as files
- Personal agenda for members and, in the browser only, for guests, with clash notice
- Sponsors: levels, logos, links, the Sponsors tab
- Registration tab: handoff to the host's registration, no payment on the platform
- Recordings and transcripts on abstract pages, except the two talks named in `docs/DECISIONS.md`
- Conference tab: conferences list, add a conference, programme editor
- Check sheet and release check `[H]`

## Release 5: Submissions and review (outline)

Run `/plan-release 5`. Mockup: `js/20-conf.js` (Submit tab, submission page), `js/65-admin.js` (Conference tab).

- Submission without account: form, word limit, draft with private link, preview, confirmation e-mail
- Author's submission page: status, revision when asked
- Assigning reviewers, with conflicts of interest
- Review screen with feedback field
- One final editor: decision, feedback to the author, preview, sending, history
- Export for the Book of Abstracts
- Retention: what is deleted when, per the privacy policy
- Check sheet and release check `[H]`

## Release 6: Feeds and calendar (outline)

Run `/plan-release 6`. Mockup: `js/60-members.js`, `js/45-events.js`.

- New-article list for members
- Press list for members, from public feeds, approved by an editor
- Saved searches with an e-mail digest, with one-click unsubscribe
- Events calendar view
- Check sheet and release check `[H]`

## Release 7: Ernie (outline)

Do not start before releases 1 to 6 are live and the board has decided who may use Ernie. Run `/plan-release 7`. Mockup: `js/80-ask.js`.

- Answers only from our own database, with the entries it used
- Says plainly when the database does not cover a question
- Monthly spending limit and a switch to turn it off
- Own section in the privacy policy `[H]`
