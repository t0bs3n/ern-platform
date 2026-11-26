# ERN Platform: production plan

Version 2, 12 November 2026. Prepared for the ERN Board.
Basis: the clickable mockup (Version 17) and the decisions taken while building it.
Change to version 1: the platform is our own application. It is not built on WordPress.

This plan says what to build, in which order, on what technical basis, and how to work with Claude while building it. The working rules themselves are in the second file, `CLAUDE.md`, which belongs in the root of the code repository.

## 1. Assumptions to confirm first

The plan rests on six assumptions. If one is wrong, the plan changes.

1. **We build our own platform.** Our code, our database, our design. WordPress is switched off once the platform has taken over.
2. **No server administration.** The platform runs on the existing all-inkl.com Premium web hosting (PHP and MySQL, with SSH access), where the provider looks after the server. No Node.js on the server.
3. **The hosting can do four things**: run a current PHP version, point a domain at a subfolder, run scheduled jobs (cron), and send e-mail. This is tested in release 0 before anything else is built.
4. **The work is done by Tobi and one or two volunteers, with Claude doing most of the coding.** There is no hired developer.
5. **The ERNC27 call stays on the current site.** Submissions run until 4 January 2027 and decisions go out on 15 March 2027 with the existing form and review screens. We do not move a running call.
6. **Membership stays free**, abstracts can be submitted without an account, and no approval time is promised to applicants.

## 2. What we build, and what we leave out

Everything in the mockup is in scope, in seven releases after the foundations (section 4). Ernie comes last. Three things are deliberately left for later, because each needs something we do not have yet:

| Left for later | What it needs first |
|---|---|
| Link to Canvas, Moodle and other learning platforms (LTI) | A university that wants it, and a decision on who supports it. Links and downloads give a complete course without it. |
| Our own Discord bot | A small always-on service outside the web hosting, and a person who runs it. Until then, the RSS feeds bring news and new articles into Discord. |
| Finished teaching packages as Word, PDF and PowerPoint | One real lecture with real slides. The first autumn lecture produces the template. |

## 3. Technical basis

### 3.1 The recommendation

**Our own application in PHP with the Laravel framework and a MySQL database, on managed web hosting.**

Pages are produced on the server and use the mockup's styles and its small scripts directly. There is no separate front-end application and no build step on the server.

Why this and not something else:

- **It is ours.** Every screen, every table and every rule is in our repository. Nothing depends on a theme, a plugin shop or a page builder.
- **It needs no server administration.** PHP and MySQL are what managed hosting offers everywhere. Moving to another provider means copying files and one database.
- **We do not write the dangerous parts ourselves.** Laravel is a toolkit, not a ready-made product. It brings tested building blocks for login, password reset, permissions, forms, database changes, e-mail and scheduled jobs. Writing these from nothing is where own platforms get hacked.
- **Claude writes it well.** It is one of the most common ways to build web applications, so the models know it thoroughly.
- **Data stays in Europe** with the hosting provider we already have a contract with.

### 3.2 The alternative, if the hosting fails the test

A TypeScript application (for example SvelteKit or Next.js) on a managed platform, with a managed database in an EU region. Also no server administration and also our own code. The price: two or three new providers, monthly fees, data-processing agreements with each, and more moving parts. We take this route only if release 0 shows that the hosting cannot run the recommendation.

### 3.3 What "own platform" costs compared with extending WordPress

Said plainly, so the board decides with open eyes:

- **More to build.** Login, member accounts, editing of pages and news, and the submission form all have to be built by us. With Claude this is weeks of work, not months, but it is real work.
- **Security updates are our job.** Somebody updates the framework a few times a year. This is one command and a test run, and it goes into the calendar.
- **One cutover.** On one day the domain moves from the old site to the new one. Addresses that people have linked to must keep working.

What we gain: one coherent system that does exactly what the mockup shows, without working around someone else's product.

### 3.4 How the mockup maps onto the platform

| Part of the mockup | Built as |
|---|---|
| Look and navigation | Server-side page templates with the mockup's CSS |
| Public pages, news, conduct, imprint | A small content area in the admin section, so that the board edits texts without code |
| Members, application, approval, roles, profile, directory | Our own user accounts with roles: Member, Reviewer, Final editor, Conference team, Database Lead, Lecture team, Editor, Admin |
| Database, reading lists, publication pages | Our own tables with full-text search. Imported once from papers.esportsresearch.net. |
| Conference: programme, agenda, sponsors, registration handoff | Our own tables. Accepted ERNC27 abstracts are imported from the current site's export. |
| Submission, review, final decision | Our own module, first used for the ERNC28 call |
| Teaching library, lecture pages, proposals, "My lectures" | Our own tables and editor |
| New articles and press | A daily scheduled job that asks Crossref, OpenAlex and PubMed, and reads the press feeds. Entries wait in a queue for the Database Lead. |
| Ernie | A server-side function. It searches our own database first and sends only the matching entries to the Claude API. The API key stays on the server. |
| Concern form | A form that sends to the Ombudsperson only and stores nothing in the platform |
| Feeds (new, not in the mockup) | RSS feeds for news, new database entries and saved searches, and a calendar feed (.ics) for events and deadlines. Public content only. They need no account and cost nothing to run. An existing Discord bot can post each new item into an ERN channel, which gives a first Discord link without our own bot. |

### 3.5 The old site

- The WordPress site stays live until the platform has taken over the domain.
- The new platform grows on a subdomain (for example `next.esportsresearch.net`), closed to search engines.
- At cutover, every old address is redirected to its new page.
- After cutover, a full copy of the old site and its database is kept in the ERN folder as an archive. The ERNC26 and ERNC27 submissions are exported first.

## 4. Releases

Each release is usable on its own. The order follows the ERN calendar, not the order of the mockup.

| Release | Content | Why now | Target |
|---|---|---|---|
| **0. Foundations** | Hosting test (login, database, scheduled job, e-mail on a subdomain), code repository, staging site, backups, invented test data, page frame with header, footer and styles, privacy policy draft | If the hosting fails the test, we switch to the alternative before any real work is lost | January 2027 |
| **1. Site and members** | All public pages and news, content editing, online application, approval by the board, welcome e-mail, password flows, profile with "update your details", directory, member overview, rights editor, concern form, RSS feed for news, calendar feed (.ics) for events and deadlines. **Cutover of the domain.** | Ends the duplicate applications and the two-monthly manual review. Cutover comes after the ERNC27 decisions are out. | May 2027 |
| **2. Database** | Import from the current database, search and filters, publication pages, reading lists, saved library, queue for new articles, "report a problem", RSS feed for new entries and for each saved search | The core research offer | July 2027 |
| **3. Teaching library** | Teaching units, lecture pages, autumn programme with draft and publish, proposals, "My lectures", unit editor, packages | The autumn series starts in September 2027 | August 2027 |
| **4. Conference pages** | ERNC27 programme by day and session, personal agenda, sponsors, registration handoff, Books of Abstracts as pages with talks and transcripts | ERNC27 is on 9 to 11 November 2027 | September 2027 |
| **5. Submissions and review** | Submission without account, drafts, reviewer assignment, feedback, one final editor, decision preview and sending, author's submission page | Must be ready when the ERNC28 call opens | Before the ERNC28 call |
| **6. Feeds and calendar** | New-article list and press for members, events calendar, saved searches with e-mail | Needs the database and the member area in place | After ERNC27 |
| **7. Ernie** | Ernie, the assistant that answers from our own database | The last feature. It is added only when everything else runs, because it depends on a complete database, has running costs and needs its own privacy text. The platform is complete without it. | 2028 |

The dates are targets tied to ERN's own deadlines. They are not effort estimates. If a release slips, the old site carries on: nothing is switched off before its replacement works.

**Each release is done when:**

1. Every screen of that part works on the staging site with test data.
2. One person who did not build it has completed the tasks in the test sheet without help.
3. It works on a phone, with the keyboard only, and at 200% zoom.
4. A second model has reviewed the code for security (section 5.4).
5. There is a backup from the same day, and a written way back.

## 5. How to work with Claude

### 5.1 The tools

- **Claude Code**, in the code repository: all building, testing and reviewing of code.
- **Claude in Cowork**, on the ERN folder: planning, texts, test sheets, importing and cleaning data, board papers. This is where this plan was written.

### 5.2 Which model for which job

Models and prices as listed in Anthropic's documentation on 12 November 2026. Prices are in US dollars per million tokens, input / output. Check the documentation before budgeting, because both change.

| Job | Model | Why |
|---|---|---|
| Everyday building: page templates, forms, styles, lists, small features, tests, import scripts | **Claude Sonnet 5.5** (`claude-sonnet-5-5`, $2 / $10) | Does ordinary web development well and fast. Most of the hours are here, so this is where the saving is. |
| Planning a release, the data model, login, roles and permissions, anything that touches member data, the cutover, reviewing a finished change, a bug Sonnet did not solve | **Claude Opus 5.5** (`claude-opus-5-5`, $4 / $20) | Stronger reasoning for the decisions that are expensive to get wrong. With our own platform there are more of these than with a ready-made system. |
| The overall architecture at the start, a second opinion once per release, a problem Opus failed on twice | **Claude Fable 5.2** (`claude-fable-5-2`, $8 / $40) | The most capable model. Rarely needed. Costs four times as much as Sonnet. |
| Inside the running platform: is this new article about esports, which topic, tidy a transcript, rewrite a search query | **Claude Haiku 5.5** (`claude-haiku-5-5`, from $0.10 / $0.50) | Many small, simple jobs. Cheap and fast. |
| Inside the running platform: Ernie's answers | **Claude Sonnet 5.5** | Answers must stay close to the sources and say when the evidence is thin. |

Rules of thumb:

- **Start with Sonnet.** Move up only when it has failed once on the same task, or when the task is on the Opus list above.
- **Never let the model that wrote a change be the only one that reviews it.** Sonnet builds, Opus reviews.
- **Move down when the job is repetitive.** Renaming, reformatting and bulk checks go to Haiku.
- The mockup was built on Fable. That was right for fast iteration on a prototype. It is the expensive way to write production code.

### 5.3 The working loop

One task is one screen or one function of the mockup, small enough to finish in a sitting.

1. **Brief.** Write the task in a few lines: what the user can do afterwards, which mockup screen shows it, which data it touches, what must not change, how we test it. A template is in `CLAUDE.md`.
2. **Plan (Opus).** Ask for a plan before any code. Read it. Correct it.
3. **Build (Sonnet).** One branch per task. Claude writes the code and the tests, and runs them.
4. **Review (Opus).** A fresh session reads only the change and looks for security holes, missing permission checks and broken edge cases.
5. **Staging.** The change goes to the staging site, never straight to the live site.
6. **Human check.** Someone clicks through it next to the mockup, on desktop and phone.
7. **Release.** Backup, deploy, a note in the change log.

Keep sessions short: one task, then start a new session. Long sessions get slower, more expensive and less careful.

### 5.4 Rules that are not negotiable

- **No real member data in development.** Staging and tests use invented people. The member list, application exports and concern reports never go into a prompt or a test database.
- **Secrets stay on the server.** API keys and passwords are never in the repository and never pasted into a chat.
- **Login and permissions use the framework's own building blocks.** We do not invent our own password storage, session handling or permission checks.
- **Every screen checks who is asking.** A page for reviewers checks the reviewer role on the server, not only in the menu.
- **The concern form stores nothing in the platform** and changes only with the Ombudsperson's agreement.
- **Nothing is deleted without a backup from the same day.**
- **The privacy policy is updated before a feature that stores new personal data goes live.** This applies to accounts, the directory, saved libraries and Ernie.

## 6. Running costs

Building costs the Claude subscription we already use. Hosting stays as it is. The running platform adds usage-based costs for three features. Until Ernie is added in the last release, only the article check costs anything. The figures below are estimates from stated assumptions, not quotes.

| Feature | Assumption | Estimate |
|---|---|---|
| Checking a newly found article (Haiku) | About 2,000 tokens in, 200 out per article | Well under one cent per article |
| One question to Ernie (Sonnet) | About 6,000 tokens in (the matching entries), 600 out | About 2 cents per question |
| Ernie at 1,000 questions a month | As above | About 20 US dollars a month |
| Transcripts | Taken from the video platform's automatic captions, then checked by a person | No model cost if captions are used as they are |

The lever is who may use Ernie. Members only keeps the cost small and predictable. Open to everyone needs a monthly limit.

## 7. Open decisions

These block specific releases. Each needs an owner on the board.

| Decision | Blocks |
|---|---|
| Who besides Tobi holds access to the all-inkl.com account, the domain and the repository | Release 0 |
| Who writes and signs off the privacy policy | Releases 1 and 7 |
| Who may edit pages and news | Release 1 |
| Whether the platform replaces papers.esportsresearch.net or takes over its entries | Release 2 |
| Who is Database Lead, and who checks the queue of new articles | Release 2 |
| Who forms the lecture team, and which permissions speakers sign for recording and slides | Release 3 |
| The real dates of the call for the autumn series | Release 3 |
| When the ERNC28 call opens, and who is its final editor | Release 5 |
| Whether Ernie answers everyone or members only | Release 7 |

## 8. Risks

- **Few hands.** The plan depends on two or three people. Each release names one owner and one stand-in, and everything they know is written into the repository.
- **Our own code, our own mistakes.** There is no vendor who patches for us. That is why login and permissions use the framework's parts, every change gets a second review, and updates have a fixed date.
- **Member data.** A mistake here is the one that damages ERN. Member features get the stronger model, a second review and test data only.
- **The cutover.** A bad cutover loses visitors and breaks links. It is rehearsed once on staging, done on a quiet day, and the old site stays ready to switch back for two weeks.
- **Scope.** The mockup holds more than a first version needs. Each release ships the screens people need for a real task, and nothing is added because it would be nice.
- **Automatic content.** Articles found automatically can be wrong. Nothing appears without a person approving it.
- **Not yet tested with users.** The mockup was reviewed by experts, not tried by newcomers. The four task sheets come before release 1 is built.

## 9. The next two weeks

1. The board confirms the six assumptions in section 1 and names owners for the decisions in section 7.
2. Create a private code repository and add `CLAUDE.md`.
3. Hosting test: an empty application on a subdomain that can log in, write to the database, run one scheduled job and send one e-mail.
4. Ask Fable once for the overall architecture and data model, against the whole mockup. Ask Opus for the plan of release 1.
5. Write the four task sheets (newcomer, teacher, speaker, volunteer) and try them on the mockup with one person each.

## Sources

- Mockup: https://claude.ai/artifact/3xy2XMMyknQ2jHrQ7wqD9R
- ERN folder: `Website/README.md` (what the current site consists of)
- Models and prices: https://platform.claude.com/docs/en/models/overview and https://platform.claude.com/docs/en/about-claude/models/choosing-a-model (read 12 November 2026)
