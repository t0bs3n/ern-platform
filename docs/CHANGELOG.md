# Change log

One line per finished task, newest at the bottom: date, task id, what someone can do now.
- 2026-11-26 R0-01: The build environment is checked and ready (PHP 8.3.6, Composer 2.8.12, git 2.43.0, package registry reachable).
- 2026-11-26 R0-02: An empty Laravel 13 application named "Esports Research Network" starts on a developer's computer.
- 2026-11-26 R0-03: `composer check` says whether the code is fit to commit, and GitHub runs it on every pull request.
- 2026-11-26 R0-04: Every page shows the ERN header, navigation, mobile menu and footer in the ERN look, and unknown addresses get a "Page not found" page in the same frame.
- 2026-11-26 R0-05: A member signs in and out at /members/sign-in, and resets a forgotten password by e-mail. Unapproved people cannot sign in.
- 2026-11-26 R0-06: The platform knows eight roles and checks on the server who may open each area; people without the right see "No access".
- 2026-11-26 R0-07: `php artisan migrate:fresh --seed` fills a local database with one test user per role and twenty invented members.
- 2026-11-26 R0-08: People with an admin role open /admin and see only the tabs their roles allow; admins see a System page with the version, the environment and the last scheduled run.
- 2026-11-26 R1-01: Editors write and change news posts and pages in the admin area (Content tab), with a preview, drafts and images, without code.
- 2026-11-26 R1-02: Visitors see the full start page: hero, three starting points, the featured event, "Coming up", the latest news, the mission and the ways to get involved.
- 2026-11-26 R1-03: Visitors read the news list (with pages) and single posts, and feed readers subscribe to /feed/news.xml.
- 2026-11-26 R1-04: Visitors read About Us (goals, members, mission, board, volunteers, history) and Get Involved; editors change the board, the volunteers and the texts.
- 2026-11-26 R1-05: Visitors read the Code of Conduct, the Imprint and the Privacy Policy; the live site cannot go up while the privacy policy is a placeholder.
- 2026-11-26 R1-06: Visitors see all conferences, the summary of each past one and the ERNC27 call with its dates; the conference team changes dates in the admin area.
- 2026-11-26 R1-07: Visitors find the podcast with its episodes, and ERN Live with the Twitch channel; nothing from Twitch or YouTube loads before a click.
- 2026-11-26 R1-08: Visitors see upcoming events and deadlines in one list and subscribe to them in their calendar through /events.ics; editors add events.
- 2026-11-26 R1-09: A researcher applies for membership online at /join and gets a copy by e-mail; nobody can use the form to find out who is a member.
