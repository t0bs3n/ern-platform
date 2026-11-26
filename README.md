# ERN Platform: start here

This is the code repository of the ERN platform: a Laravel application, together with everything Claude Code needs to build it step by step: the rules, the technical decisions, the mockup as specification, and a backlog of tasks in the order they should be built. Releases 0 and 1 are built, except the steps that wait for you or for the hosting (from R0-09 on). What comes next is in `docs/BACKLOG.md`, and what waits for you is in `docs/HUMAN_TODO.md`.

You open the repository in Claude Code and type `/build`. Claude then works through the backlog on its own and stops only where it needs you.

## Where to run it

The code lives in a private GitHub repository. You can work on it in two ways, and you can switch between them at any time, because everything Claude finishes is saved to GitHub.

| Way | Good for | What you need |
|---|---|---|
| **Claude Code in the browser**, connected to GitHub | The normal case. Nothing to install. It keeps working while your laptop is closed. | The GitHub repository connected to Claude Code |
| **Claude Code in the Claude desktop app** | When you want to look at the application on your own Mac while it is built | PHP and Composer on the Mac (Laravel Herd is the simplest installer) and a local copy of the repository, kept outside iCloud |

Start in the browser. The first tasks need nothing from your Mac.

## Run it on your own computer

You need PHP 8.3 and Composer (on a Mac, Laravel Herd installs both). Then, in the repository folder:

1. `composer setup` installs the packages and creates a local database.
2. `php artisan migrate:fresh --seed` fills the database with the content and invented members.
3. `php artisan serve` starts the site at http://localhost:8000. Sign in as `admin@example.test`. The password of all invented users is `SEED_PASSWORD` in your `.env` file. It works only on your computer: the server never creates these users.

`composer check` runs the code style check and all tests. It must pass before anything is merged.

## How a run looks

Each run works on its own branch and ends with a pull request on GitHub: a page that lists what was done. GitHub runs all tests on it automatically. When you are content, you press "Merge". That is your look at the work, and it is the only click a normal run needs from you. The next `/build` starts from the merged state.

To see the result with your own eyes, either open the staging site after an upload (from task R0-11 on), or open the repository in the desktop app and let Claude start the application on your Mac.

## The commands

| Command | What it does |
|---|---|
| `/build` | Works through the backlog task by task until it reaches a task that needs you, or a problem it cannot solve. This is the one you use most. |
| `/next` | Does exactly one task and stops. Good for watching how it works. |
| `/status` | Shows where the build stands: what is done, what is next, what waits for you. |
| `/plan-release 2` | Turns the outline of a later release into detailed tasks, against the mockup. Runs on the stronger model. Needed once before releases 2 to 7. |
| `/done R0-09` | You tell Claude that you have done your part of a task that was waiting for you. |

If Claude Code does not recognise a command, say: "Read `.claude/commands/build.md` and follow it." The commands are plain text files in `.claude/commands/` and you can read and change them.

## What runs by itself, and what does not

**By itself:** for each task Claude reads the brief, plans, writes the code and the tests, runs them, has a second model review the change, fixes what the review finds, commits, and ticks the task off in `docs/BACKLOG.md`. Then it takes the next one. Tasks R0-01 to R0-08 need nothing from you, so the first run gets you a working application with login, roles, test members and the ERN look.

**Not by itself:** everything marked `[H]` in the backlog. These are the points where only you can act or decide:

- Merging each pull request on GitHub.
- Anything in your all-inkl.com account: subdomain, database, mailbox, SSH access.
- Storing the access details for the server in GitHub's protected settings. They never go into a chat.
- Starting each upload, to staging and to live, with a button on GitHub.
- The privacy policy and the agreement of the Ombudsperson for the concern form.
- Importing the real member list. This happens on the server only, never in the repository and never in a chat.
- The check at the end of each release: you, or someone who did not build it, click through the staging site next to the mockup.
- The cutover of the domain.

When Claude reaches such a point it stops and tells you in a few lines what it needs. You do it, type `/done <task>`, then `/build` again.

## Which model

The model choice is built into the setup, so you do not have to think about it:

- The session runs on **Sonnet 5.5** (set in `.claude/settings.json`). It does the building.
- The **reviewer** and the **planner** run on **Opus 5.5** (set in `.claude/agents/`). They plan the sensitive tasks and check every change.
- **Fable 5.2** is not used automatically. Switch to it by hand (`/model`) only for the one architecture review in task R0-13, or when Claude reports that it failed twice on the same problem.

## Good to know

- **The backlog is the memory.** Every finished task is ticked in `docs/BACKLOG.md` and saved to GitHub. You can close the session at any time. The next `/build` continues where the last one stopped.
- **Usage limits.** A long run can reach the limit of your Claude subscription. Nothing is lost. Wait, then type `/build` again.
- **Nothing reaches the server without you.** Claude saves its work to GitHub only. Uploads to staging and live happen when you press the button.
- **No real data.** The repository and every development copy only ever hold invented members.
- **If something looks wrong,** say so in plain words. Claude can show you the last changes (`git log`) and undo a task.

## What is in this folder

| Path | What it is |
|---|---|
| `CLAUDE.md` | The rules. Claude reads this at the start of every session. |
| `docs/BACKLOG.md` | All tasks in order, with their status. |
| `docs/DECISIONS.md` | The technical decisions, the address plan, and what was decided during the mockup phase. |
| `docs/PRODUCTION_PLAN.md` | The plan the board saw: releases, dates, risks. |
| `docs/HUMAN_TODO.md` | Your own list: everything that waits for you, with instructions. |
| `docs/mockup/` | The source of the clickable mockup. It is the specification for look, wording and behaviour. |
| `docs/briefs/` | One short note per finished task: what was built, what was tested, what was not. |
| `docs/CHANGELOG.md` | One line per finished task. |
| `.claude/` | The commands, the reviewer, the planner and the permissions. |
| `.github/workflows/` | The automatic test run (on SQLite and on MySQL). The upload buttons follow in R0-10. |
| `docs/checks/` | The check sheet for each release, for a person who did not build it. |
