# CLAUDE.md: how we work on the ERN platform

Claude reads this file at the start of every session. People read it before their first task. Keep it short and keep it true.

## What this is

The platform of the Esports Research Network (ERN e.V.) at esportsresearch.net. It is our own application: PHP with the Laravel framework, MySQL on the server, pages produced on the server. It runs on managed web hosting (all-inkl.com). There is no Node.js on the server and no build step anywhere.

Read these before working:

- `docs/DECISIONS.md`: the technical decisions and the address plan. They are binding. If a task seems to need a different decision, stop and say so.
- `docs/BACKLOG.md`: the tasks, in order, with their status. This is the single source of truth for what is done.
- `docs/mockup/`: the specification for look, wording and behaviour. Read `docs/mockup/README.md` first.

The owner of the project is Tobi. He is not a developer. Write to him in plain words, say what happened and what you need, and leave out jargon he does not need.

## Rules that always apply

1. **Never work on the live site, and never push to main.** Changes go to a branch, then through a pull request that Tobi merges, then to staging, then live. Uploading is always started by Tobi on GitHub. You have no access to the server and you do not try to get it.
2. **No real personal data.** Use the invented members from the seeders. Never ask for, paste, read or load the real member list, application exports or concern reports. If real personal data appears in front of you, stop and say so.
3. **No secrets in the repository or the chat.** Keys and passwords live in the server's `.env` file and in GitHub's protected settings (secrets). You never read them and never ask Tobi to paste one.
4. **Use the framework's parts for login, passwords, sessions and permissions.** Do not write your own.
5. **Check permissions on the server.** Every page and every action checks the user's role through a policy or gate. Hiding a menu item is not a check. Every such check has a test that a wrong role is refused.
6. **Validate every input, escape every output, keep the CSRF protection on.** Database access goes through the framework. No SQL assembled from user input.
7. **Every database change is a migration.** Never change a database by hand.
8. **Keep it plain.** Blade templates, one stylesheet, small scripts without a framework. No npm, no Vite, no Tailwind. A new Composer package needs a reason written into `docs/DECISIONS.md`.
9. **The concern form stores nothing** and is built only in its own task.
10. **Ask before you delete** anything outside the task's own new files.
11. **Stay in scope.** Do what the task says. Write ideas for extras into the hand-over note. Do not build them.
12. **Say what you did not check.** Every hand-over note lists what was tested and what was not.

## Text on the site

- Take wording from the mockup. Do not rewrite labels or page texts without a reason.
- US spelling ("organization", "program", "behavior"), also where the mockup uses UK spelling. Proper names keep their own spelling.
- No em dashes. Use a colon, a comma or a full stop.
- Plain words. A label says what happens when you click it.
- "Ombudsperson" is the role. Do not name the person.
- People in test data are invented. Institutions of invented people carry "(example)".

## Which model

| Job | Model |
|---|---|
| Building a task | Sonnet 5.5 (the session default) |
| Planning a task marked `Model: opus`, planning a release, reviewing every change | Opus 5.5 (the `planner` and `reviewer` agents) |
| Architecture review in R0-13, or a problem that failed twice | Fable 5.2, switched on by Tobi by hand |
| In the running platform: classifying articles, tagging | Haiku 5.5 |
| In the running platform: Ernie (last release, do not build earlier) | Sonnet 5.5 |

The model that wrote a change does not review it alone. The review always goes to the `reviewer` agent.

## Where the work happens

The repository is on GitHub. A session runs either in Claude Code in the browser or in the Claude desktop app on Tobi's Mac. Work the same way in both. At the start of a session, find out what you have: if `php` or `composer` is missing, say so and stop.

One run of `/build` or `/next` uses one branch, `build/<date>-<first-task-id>`, created from the current main. Every finished task is one commit on that branch, pushed at once so that nothing is lost. At the end of the run, open one pull request to main (or update the one that exists) with a plain-language summary. Tobi merges it. Never merge it yourself.

To open the pull request, use `gh pr create`. Browser sessions can refuse that command. Then use the plain route: `gh api repos/<owner>/<repo>/pulls -f title='<title>' -f head='<branch>' -f base='main' -f body='<text>'`.

If an earlier pull request is still open when a run starts, continue on its branch.

## The loop for one task

The commands `/next` and `/build` run this loop. The steps:

1. **Pick** the first task in `docs/BACKLOG.md` marked `[ ]` whose needs are met. Tasks marked `[H]` wait for Tobi. If nothing can be done without him, stop and tell him what is needed.
2. **Read** the task, the mockup files it names, and the relevant part of `docs/DECISIONS.md`.
3. **Plan.** For a task marked `Model: opus`, ask the `planner` agent for the plan and follow it. Otherwise plan briefly yourself: files, data, permission checks, tests.
4. **Build** the code and the tests. A task without a test for its main path and for its permission checks is not finished.
5. **Check:** run `composer check` (code style and all tests). It must pass completely.
6. **Review:** give the change to the `reviewer` agent. Fix everything it reports under points 1 and 2. Run the check again. After two rounds with open findings under 1 or 2, stop, undo the task's changes on the branch, mark the task `[!]` and explain.
7. **Hand over:** write `docs/briefs/<id>.md` (what was built, how to see it, what was tested, what was not, ideas left out). Add one line to `docs/CHANGELOG.md`. Mark the task `[x]` with the date.
8. **Commit** the task as one commit that starts with its id, and push the branch.

Never mark a task `[x]` if the check fails or a finding under 1 or 2 is open.

## Status marks in the backlog

| Mark | Meaning |
|---|---|
| `[ ]` | Open |
| `[x]` | Done, checked and reviewed |
| `[H]` | Waits for Tobi. The task says what he has to do. He confirms with `/done <id>`. |
| `[!]` | Blocked. The line below says why. |

## Done means

- `composer check` passes, in the session and in the automatic test run on GitHub.
- The review has nothing open under points 1 and 2.
- The page looks and reads like the mockup at phone width and desktop width.
- The hand-over note and the change log line exist.

A release is done when, in addition, a person who did not build it has clicked through it on staging (the last task of each release).

## Upkeep

- Framework and package updates: four fixed dates a year, and at once for a security release. Update, run the check, upload through staging.
- Backups: the database every night, kept for 30 days. Once a month, restore one on staging to prove it works.

## When something goes wrong

- An upload breaks the live site: go back to the previous version first, investigate second.
- Personal data turns up where it should not be: stop, tell Tobi, write down what happened.
- Stuck on the same problem twice: stop, mark the task `[!]`, and write down the error and what was tried. Tobi then retries with a stronger model.
