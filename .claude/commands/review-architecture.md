---
description: One-time review of the architecture and data model (run on the strongest model)
---

This is task R0-13. Tobi runs it by hand on the strongest model. Write no application code.

1. Read `CLAUDE.md`, `docs/DECISIONS.md`, `docs/PRODUCTION_PLAN.md`, `docs/BACKLOG.md`, the application as it stands, and the mockup in `docs/mockup/` (start with its README, then `js/90-router.js`, then the page files).
2. Write `docs/DATA_MODEL.md`: every table the platform will need through release 6, its fields, its relations, who may read and write it, and when its personal data is deleted.
3. Write `docs/ARCHITECTURE_REVIEW.md`, at most two pages, in plain words:
   - Decisions in `docs/DECISIONS.md` you would change, each with the reason and the cost of not changing it.
   - The five largest risks for a platform built and run by two or three volunteers, each with one concrete precaution.
   - Tasks in the backlog that are missing, too large or in the wrong order.
   - What the hosting must be able to do that R0-11 does not test yet.
4. Change nothing else. Do not edit the decisions or the backlog: Tobi decides what to take over.
5. Mark R0-13 as `[x]` with the date, commit the two documents on a branch and open a pull request.

Tell Tobi the three points he should decide on, each in two lines.
