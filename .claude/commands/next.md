---
description: Do exactly one task from the backlog, then stop
---

Do one task from `docs/BACKLOG.md`, following "The loop for one task" in `CLAUDE.md` exactly.

1. Read `CLAUDE.md`, `docs/DECISIONS.md` and `docs/BACKLOG.md`.
2. Set up the branch as described under "Where the work happens" in `CLAUDE.md`: continue on the branch of an open pull request if there is one, otherwise start `build/<date>-<task-id>` from the current main. If there are changes that nobody committed, stop and say what you found.
3. Pick the task: $ARGUMENTS if a task id was given, otherwise the first task marked `[ ]` whose "Needs" are all `[x]`.
4. If there is no such task because the next ones wait for Tobi (`[H]`) or are blocked (`[!]`), do not build anything. Tell Tobi in plain words which task waits, what exactly he has to do (from the task and `docs/HUMAN_TODO.md`), and how he tells you it is done (`/done <id>`).
5. If the next open task belongs to a release that is still an outline, stop and tell Tobi to run `/plan-release <n>`.
6. Otherwise run the loop: read the mockup files the task names, plan (through the `planner` agent if the task says `Model: opus`), build with tests, `composer check`, review through the `reviewer` agent, fix, hand-over note, change log, mark `[x]` with today's date, commit, push.
7. Open the pull request to main, or update its description if it exists. Do not merge it.

Finish with a report of at most six lines for Tobi: what someone can do or see now, the link to the pull request, what was not checked, and what comes next.
