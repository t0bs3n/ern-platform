# The mockup: how to read it

This folder holds the source of the clickable mockup (Version 17) that the ERN board approved. Published version: https://claude.ai/artifact/3xy2XMMyknQ2jHrQ7wqD9R

It is the specification for **look, wording and behaviour**. It is not code to port.

## What to take from it

- **Wording.** Headings, labels, help texts, error messages and e-mail texts. Take them as they are.
- **Structure.** Which blocks a page has, in which order, and what each control does.
- **Styles.** `theme.css` is the stylesheet of the current ERN site. `app.css` adds everything the mockup needed on top. Together they become `public/css/ern.css`. Class names starting with `x-` come from `app.css`.
- **Example content.** `data.json` holds the content the mockup shows: news, conferences, abstracts, publications, pages. Use it for seeders.

## What not to take from it

- **The JavaScript logic.** The mockup is a single page that keeps everything in memory. The platform renders pages on the server and stores data in the database. Read the scripts to understand what a screen does, then build it properly.
- **The mock parts.** The bar at the top, the role switcher, the "Guide" page, and every notice that nothing is really sent.
- **Invented people as real members.** The people in `data.json` under `people` and `me` are examples. They may be used as test data. Authors of abstracts and publications are real and public.

## The files

| File | What it shows |
|---|---|
| `shell.html` | Page frame: header, navigation, mobile menu, footer, Ernie panel |
| `js/00-core.js` | State and helpers. Shows which things a visitor or member can save or change. |
| `js/10-home.js` | Start page |
| `js/20-conf.js` | Conferences, sponsors, ERNC27 tabs (Call, Submit, Programme, Registration, Sponsors), submission journey, author's submission page, ERNC26 programme with agenda, abstract view |
| `js/25-pdf.js` | Book of Abstracts viewer |
| `js/27-talk.js` | Transcripts, Teaching library, teaching units, lecture pages, course guide, propose a lecture, My lectures |
| `js/28-zip.js` | Download of a teaching package as a zip file |
| `js/30-db.js` | Database: search, filters, reading lists, publication pages, report a problem |
| `js/35-saved.js` | Saved library |
| `js/40-live.js` | Live page |
| `js/45-events.js` | Events and deadlines |
| `js/50-about.js` | About, Get Involved, Join, Code of Conduct, Report a Concern, Imprint, form validation |
| `js/60-members.js` | Member overview, directory, researcher pages, sign-in, password reset, activation, profile |
| `js/65-admin.js` | Admin area: Overview, Members, Conference, Database, Teaching, Content |
| `js/70-guide.js` | Explains the mockup. Not built. |
| `js/80-ask.js` | Ernie. Last release. |
| `js/90-router.js` | Which address shows which page, and what every button and form does |
| `data.json` | Example content |
| `img/` | Images and the member map |
| `owl.svg` | Ernie's owl |

To find what a button does: search its `data-act` name in `js/90-router.js`.
