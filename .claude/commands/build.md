---
description: Work through the backlog task by task until something needs Tobi
---

Work through `docs/BACKLOG.md` one task after the other. For each task, do exactly what `.claude/commands/next.md` describes.

Keep going after a finished task without asking. Stop only when one of these is true:

- The next task waits for Tobi (`[H]`) and no other open task has all its needs met.
- A task ended blocked (`[!]`).
- The next open task belongs to a release that is still an outline.
- `composer check` fails before you start. Then repair nothing blindly: say what fails.
- The automatic test run on GitHub is red for the open pull request. Repair that first.
- You have finished eight tasks in this run. Then stop for a look by Tobi.

Between tasks, keep your context small: each task starts from the files, not from what you remember of the previous task.

When you stop, give Tobi one short report:

1. Tasks finished in this run, one line each, in plain words.
2. The link to the pull request, and the reminder to merge it when the tick is green.
3. Why you stopped, and exactly what he has to do now.
4. Anything you could not check.
