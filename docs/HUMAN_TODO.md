# What waits for Tobi

Everything in the build that only you can do. Each point belongs to a task in `docs/BACKLOG.md`. When you have done one, tell Claude Code: `/done <task>`.

Never type a password, a key or anything from the real member list into Claude Code. Passwords go into the `.env` file on the server and into GitHub's secrets. Claude reads neither.

## Every run: merge the pull request

When a run ends, Claude gives you a link to a pull request on GitHub. Wait for the green tick of the automatic test run, read the summary, press "Merge". If the tick is red, tell Claude: it repairs it.

## Once, after R0-03: protect the main branch

Claude tells you the three clicks in the hand-over note. Afterwards nothing can reach main without a green test run.

## R0-09 Prepare the hosting (about 45 minutes)

In the all-inkl.com customer area:

1. Create the subdomain `next.esportsresearch.net`. Set its folder to `ern-next/current/public`.
2. Switch on SSL for the subdomain, and set its PHP version to 8.3 or newer. Tell Claude which version you set.
3. Create a new MySQL database. Note its name, user and password for step 6.
4. Create a mailbox for outgoing mail, for example `platform@esportsresearch.net`. Note the password for step 6.
5. Create an SSH key pair for uploads and store the public key at the hosting. Claude explains the steps when you reach this task. You carry them out yourself.
6. On the server, create the file `ern-next/shared/.env` from the template `.env.staging.example` in the repository, and fill in the database and mailbox details there. You can do this with the hosting's file manager or any file transfer program.
7. Protect the subdomain with a directory password, so that the staging site is not public.

On GitHub, in the repository's settings under "Secrets and variables":

8. Store the host name, the user name, the path and the private key as secrets, under the names Claude gives you in `docs/DEPLOY.md`. This is the only place they are typed in. Never into a chat.

## R0-10 First upload to staging

On GitHub, open "Actions", choose "Deploy to staging", press "Run workflow". Green means it worked. If it is red, copy the last lines of the log to Claude. The log contains no passwords.

## R0-11 Hosting test

Open the staging site. Sign in with the test admin. Check that the test e-mail arrived and that the page "System" in the admin area shows a scheduled job that ran in the last hour. If one of these fails, tell Claude what you saw. If it cannot be fixed, the production plan names the alternative.

## R0-13 Architecture review (optional, 20 minutes)

Switch the model to Fable 5.2 with `/model`, then type `/review-architecture`. Read the answer. Switch back to Sonnet.

## Release 1

- **R1-05 Privacy policy.** The text has to come from the board. Until then the page says that it is being prepared, and the platform must not go live.
- **R1-17 Concern form.** Get the Ombudsperson's agreement and the address the form sends to. The address goes into the server's `.env`.
- **R1-19 Member import.** Export the current member list. Place the file on the server yourself, with the hosting's file manager. Start the import with its button on GitHub, first as a trial run. The file never touches the repository or the chat.
- **R1-22 Release check.** You, or better someone who did not build it, click through staging next to the mockup, on a computer and on a phone.
- **R1-23 Cutover.** After the ERNC27 decisions are out. Claude gives you the checklist.

## Decisions the board still owes

See section 7 of `docs/PRODUCTION_PLAN.md`.
