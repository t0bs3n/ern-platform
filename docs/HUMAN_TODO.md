# What waits for Tobi

Everything in the build that only you can do. Each point belongs to a task in `docs/BACKLOG.md`. When you have done one, tell Claude Code: `/done <task>`.

Never type a password, a key or anything from the real member list into Claude Code. Passwords go into the `.env` file on the server and into GitHub's secrets. Claude reads neither.

## Every run: merge the pull request

When a run ends, Claude gives you a link to a pull request on GitHub. Wait for the green tick of the automatic test run, read the summary, press "Merge". If the tick is red, tell Claude: it repairs it.

## Once, after R0-03: protect the main branch

Claude tells you the three clicks in the hand-over note. Afterwards nothing can reach main without a green test run.

## R0-09 Prepare the hosting (about 45 minutes)

Before you start, type `/next` in Claude Code. Claude then walks you through these steps one at a time and waits for you after each one. You never type a password into the chat.

In the all-inkl.com customer area (KAS):

1. **Look before you build.** Note which folder the main domain `esportsresearch.net` points to, for example `/` or `/wordpress/`. Tell Claude the folder name (it is not a secret). Claude needs it to make sure that the new site's private files can never be opened through the main domain.
2. **Create the subdomain** `next.esportsresearch.net`. Set its folder to `ern-next/current/public`. If the customer area creates these folders by itself, leave them: the first upload replaces them.
3. **Switch on SSL** for the subdomain (Let's Encrypt), and tick "force HTTPS" (SSL erzwingen), so that every visit is encrypted. Set the PHP version of the subdomain to 8.3 or newer. Tell Claude which version you set.
4. **Create a new MySQL database.** Note its name, user and password for step 8. Tell Claude the MySQL version that the customer area shows (not a secret).
5. **Create a mailbox for outgoing mail**, for example `platform@esportsresearch.net`. Note its password, the mail server name the customer area shows for it (something like `w0123456.kasserver.com`), and the login name it shows for the mailbox (something like `m0123456`, or the address itself), for step 8.
6. **Check SSH access** in the customer area: your Premium package includes it. Note the SSH host name and user name. Claude explains how to make the key pair for uploads when you reach this step, and how to store its public half at the hosting. You carry it out yourself. The private half goes nowhere except into GitHub's secrets in R0-10.
7. **Protect the whole `ern-next` folder** (create it first, if step 2 did not) with a directory password (Verzeichnisschutz), not only the subdomain's folder. Then the staging site is not public, and the protection survives every upload. Give the user name and password to the people who test, never to Claude.
8. **Create the settings file**, only after step 7, so that it is protected from the first moment. On the server, create the folder `ern-next/shared` and in it the file `.env`, as a copy of `.env.staging.example` from the repository. Fill in only these lines, with the hosting's file manager or a file transfer program:
   - `APP_URL=https://next.esportsresearch.net`
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` from step 4
   - `MAIL_HOST` (the mail server name), `MAIL_USERNAME` (the login name of the mailbox), `MAIL_PASSWORD`, and `MAIL_FROM_ADDRESS` (the mailbox address) from step 5

   Leave `APP_KEY` empty: the first upload creates it on the server by itself.

   A Mac does not let you save a file whose name starts with a dot. Create the file directly in the hosting's file manager (WebFTP), or upload it as `env.txt` and rename it there to `.env`.

When you are done, type `/done R0-09`. The secrets for GitHub follow in R0-10, once Claude has written `docs/DEPLOY.md` with their names.

## R0-10 First upload to staging

1. On GitHub, in the repository's settings under "Secrets and variables", store the host name, the user name, the path, the private key and the server's fingerprint as secrets, under the names Claude gives you in `docs/DEPLOY.md`. This is the only place they are typed in. Never into a chat.
2. On GitHub, open "Actions", choose "Deploy to staging", press "Run workflow". Green means it worked. If it is red, copy the last lines of the log to Claude. The log contains no passwords.

## R0-11 Hosting test

Claude gives you the one line for the scheduled job (cron) at the hosting, and a button on GitHub that creates the first admin with an e-mail address you type into its form. Then open the staging site, set the admin's password with "Forgot your password?", and sign in. On the page "System" in the admin area, send yourself the test e-mail. Check that it arrived and that the page "System" in the admin area shows a scheduled job that ran in the last hour. If one of these fails, tell Claude what you saw. If it cannot be fixed, the production plan names the alternative.

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
