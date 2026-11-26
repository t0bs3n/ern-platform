# First session: set up the repository

Tobi uploaded this starter folder through the GitHub website, where hidden folders are awkward to upload. That is why the Claude Code configuration arrived in the visible folder `claude-setup/`. Put it in its place once:

1. Create the branch `setup` from main.
2. Move everything from `claude-setup/` into `.claude/` so that these exist: `.claude/settings.json`, `.claude/commands/*.md`, `.claude/agents/*.md`. Remove the empty folder `claude-setup/`.
3. Delete this file, `START.md`.
4. Read `CLAUDE.md`.
5. Commit ("Set up Claude Code configuration"), push the branch and open a pull request to main, as described under "Where the work happens" in `CLAUDE.md`.
6. Tell Tobi in three lines: the setup is done, here is the pull request to merge, and from now on he types `/build`.
7. If Tobi wants to go on at once: the new commands may only be recognised in a new session. In this session, read `.claude/commands/build.md` and follow it, continuing on the `setup` branch.

If you are not allowed to write into `.claude/`, stop and tell Tobi. He can then open the repository once in the Claude desktop app and repeat this step there.
