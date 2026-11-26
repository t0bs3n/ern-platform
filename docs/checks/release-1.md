# Release 1: check sheet

This sheet is for a person who did not build the platform. Work through it on the staging site. Keep the mockup open next to it: https://claude.ai/artifact/3xy2XMMyknQ2jHrQ7wqD9R. The column "Compare with" names the page in the mockup. You reach it by adding the name to the mockup's address, for example `#news`.

Do all twenty tasks once, on a computer. Then do the visitor tasks 1 to 8 a second time on a phone. The applicant tasks cannot simply be repeated: an address that has applied once is not taken again.

For every task, write down one of these:
- **OK**: it works as described.
- **Different**: it works, but looks or reads differently from the mockup. Say what differs.
- **Broken**: it does not work. Say what you did and what you saw.

Send the filled-in sheet to Tobi. He passes it to Claude, and each finding becomes a new task.

## Before you start
- You need the address of the staging site and its directory password. Tobi gives you both.
- You need **two e-mail addresses of your own** that you can read during the check, called A and B below. The applicant tasks send real e-mails to them.
- You need the test admin account that Tobi set up on staging.
- **Expected limits, not faults:**
  - After five applications within an hour, the join page shows "Too many tries". If that happens, wait an hour.
  - The mockup shows more than this release builds. Only content that someone has entered appears on staging. Lists without entries are simply not shown.
- **Not part of this release.** These answer "Page not found" for now:
  - "Database", "Teaching" and the Saved library in the menu
  - "Database", "Teaching library" and "Report a Concern" in the footer
  - "Report a concern" on the Code of Conduct page
  - on the start page, the six links near the top: "Search the database", "Reading lists", "Teaching units for one class", "The autumn lecture series", "Run it as a course" and "Propose a lecture"

  In the admin area, the tabs "Database" and "Teaching" say "This part of the admin area is being built." Do not report any of these as broken.

## As a visitor (not signed in)

| # | What to do | What should happen | Compare with |
|---|---|---|---|
| 1 | Open the start page. Scroll to the end. | You see the welcome block, the next conference with its dates, the latest news and "Coming up". At the bottom are the footer with the board's e-mail address and the links Imprint, Privacy Policy and Code of Conduct. | `#home` |
| 2 | Make the window narrow, or use the phone. Open the menu at the top right and choose "About Us". | The menu opens, lists every section and closes again. The About Us page shows the board and the volunteers. The page never scrolls sideways. | `#about` |
| 3 | Open "News", then open any post. | The list shows the posts, newest first. The post opens with its date, its text and its picture, if it has one. "All news" leads back to the list. | `#news` |
| 4 | Open "Conferences" and press "Read the Call for Abstracts". | The ERNC27 page shows the call, with the key dates under "At a glance". Only the tabs "Call for Abstracts" and "Submit" exist in this release. "Submit an abstract" leads to the submission form on the current website. | `#conferences`, `#ernc27` |
| 5 | On "Conferences", press "Summary" next to ERNC25. | The summary of that conference opens, with its place, dates and description. | `#ernc25` |
| 6 | Open "Live" and "Podcast". | Before you start anything, the Live page shows a picture and the text "The player comes from Twitch. It loads only when you start it…". The player appears only after you press "Start the player here". The Podcast page lists the episodes. | `#live`, `#podcast` |
| 7 | On the start page, under "Coming up", press "All events and deadlines". Then press "Subscribe to the calendar (.ics)". | The page lists the coming dates, nearest first. The button gives a calendar file. Your calendar program can open it, and the dates appear on the right days (and at the right times, for events that have one). | `#events` |
| 8 | Type an old address of the current website into the browser: the staging address followed by `/getinvolved`, then `/ernc25-summary`. Then type an address that does not exist, such as `/nothing-here`. | The old addresses lead to the new Get Involved page and to the ERNC25 page. The address that does not exist shows "Page not found" with a way back to the home page. | (not in the mockup) |

## As an applicant

| # | What to do | What should happen | Compare with |
|---|---|---|---|
| 9 | Open "Get Involved", then "Become a Member". Use only the keyboard: Tab to move, Enter to press. Press "Send application" without filling anything in. | You can always see where you are. The form is not sent. At the top you see "Please check the marked fields." Each missing field shows its own message under it. | `#join` |
| 10 | Fill in the form with address **A** and invented details. Tick the box for the Code of Conduct and the privacy policy. Press "Send application". | The page thanks you by your first name and says that the application is received. An e-mail confirming it arrives at address A. | `#join` |
| 11 | Open "Become a Member" again and send the form with address **A** once more. | The page answers the same way, so it does not reveal that the address already applied. No second e-mail arrives. | `#join` |
| 12 | Wait until the admin has approved you (task 18). Open the link in the welcome e-mail, choose a password and press "Set password and sign in". Then press "Sign out" and open the same link a second time. | The first time, the page "Welcome to ERN" asks for a password. After that, you are signed in and see the member area. The second time, the page says "This link was already used". | `#members` |

## As a member (with address A)

| # | What to do | What should happen | Compare with |
|---|---|---|---|
| 13 | Sign in on the sign-in page. Then sign out, and use "Forgot your password?" to set a new password. Sign in with it. | Signing in leads to the member area, with your membership. Notices appear there once one is published (task 20). The password e-mail arrives, and the new password works. | `#members` |
| 14 | In the member area, open "My profile" (the page "My details"). Change your research interests, tick "Show me in the member directory" and press "Save changes". | The message "Your details are saved." appears. The member directory now lists you, with the details you allowed. | `#members` (profile) |
| 15 | Ask the admin to tick "Show me in the member directory" in the admin's own profile (or do it yourself with the test admin). Then, as member A, search the member directory for the admin's name, open their page and send a short message with "Send through ERN". | The search finds the admin. You see "Your message is sent. … gets it by e-mail and can answer you directly." The admin's e-mail address is never shown to you. If the test admin has a real e-mail address, the message also arrives there. | `#members` (directory) |
| 16 | On "My profile", press "Download my data". | A file downloads that holds your own details, and no other member's. | `#members` (profile) |

## As an admin

| # | What to do | What should happen | Compare with |
|---|---|---|---|
| 17 | Send one more application through "Become a Member", now with address **B**. Then sign in with the test admin and open "Admin area". | Under "Needs attention" you see "2 membership applications wait for the board" (A and B) with "Confirm or decline". Members who want to leave, and drafts, appear only when there are some. The tiles show "Applications to confirm", "Members" and "Drafts". | `#admin` |
| 18 | Open "Members". Under "Applications to confirm", press "Approve" for the application with address A, and "Decline" for the one with address B. | "Approved, welcome e-mail sent" appears, and the welcome e-mail with a link to choose a password arrives at A. "Declined." appears for the other, and a polite e-mail arrives at B. | `#admin` (Members) |
| 19 | In the member list, press "Edit rights" next to member A. Tick "Editor" and press "Save the rights". Sign in as member A and look at the member area. Afterwards, as admin, remove the right again. Then press "Export the list" on the members page. | The message "Rights saved for …: Member, Editor." appears. Member A now sees "Admin area" in the member area, and there the Content tab. The export is a spreadsheet file of the members. | `#admin` (Members) |
| 20 | As admin, open "Content". Press "New post", write a post, choose "Draft" and press "Save". Look at the news page as a visitor. Then edit the post, choose "Published", leave the date empty and press "Save". Do the same with "New notice". | The draft does not appear on the news page. Once published, it appears there at once, with today's date. A published notice appears in the member area under "Notices". | `#admin` (Content) |

## At the end
- Did every page look right on the phone, with nothing cut off and no sideways scrolling?
- Could you always see where you were when you used the keyboard in task 9?
- Was any text hard to read, unclear or different from the mockup? Note the page and the words.
