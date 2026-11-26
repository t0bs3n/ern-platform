---
description: Turn the outline of a release into detailed tasks
argument-hint: <release number, 2 to 7>
---

Plan release $ARGUMENTS. Write no application code.

Give this work to the `planner` agent with these instructions, then put its result into the files:

1. Read `CLAUDE.md`, `docs/DECISIONS.md`, `docs/PRODUCTION_PLAN.md`, the outline of the release in `docs/BACKLOG.md`, the mockup files the outline names, and the existing models and migrations in the application.
2. Write or extend `docs/DATA_MODEL.md` with the tables and fields this release adds, and how they connect to what exists.
3. Replace the outline in `docs/BACKLOG.md` with detailed tasks in exactly the format of release 1: id (`R<n>-01` and so on), mark `[ ]`, Model, Needs, Can, Mockup, Do, Test. Each task is one screen or one function, small enough for one sitting. Mark as `Model: opus` every task that touches personal data, permissions, e-mail to people, imports, money or deletion.
4. Mark as `[H]` everything only Tobi can do, and add it to `docs/HUMAN_TODO.md` with plain instructions.
5. End the release with an accessibility pass, a check sheet in `docs/checks/`, and the release check on staging `[H]`.
6. List open questions for Tobi at the top of the release, at most five, each with a recommended answer. Tasks that depend on an open question get it under "Needs".

For release 7 (Ernie): refuse unless releases 1 to 6 are marked done, and say why.

Afterwards commit the changed documents on a branch, open a pull request, and give Tobi a summary: number of tasks, the open questions, and what waits for him.
