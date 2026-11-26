
  /* ---------- about this mockup ---------- */
  function guide() {
    function li(x) { return '<li>' + (x[1] ? '<a href="' + x[1] + '"' + (x[3] || '') + '>' + x[0] + '</a>' : '<strong>' + x[0] + '</strong>') + (x[2] ? '<small>' + x[2] + '</small>' : '') + '</li>'; }
    var existing = [
      ['Home', '#home', 'Hero, three starting points, featured event, goals and the ways to get involved'], ['News', '#news', 'The ten latest posts'], ['Conferences', '#conferences', 'ERNC27, key dates and the past conferences'],
      ['Call for Abstracts', '#ernc27', 'The full text for ERNC27'], ['Conference summaries', '#ernc25', 'ERNC21 to ERNC26'], ['Podcast', '#podcast', 'The Esports Research Report'], ['About Us', '#about', 'Goals, mission, board, governance, history'],
      ['Get Involved', '#involved', 'Community text and the ten ways to participate'], ['Code of Conduct', '#conduct', 'Version 1.1'], ['Report a Concern', '#concern', 'The form with all its fields'], ['Imprint', '#imprint', '']
    ];
    var fresh = [
      ['Database', '#database', 'Search, filters, citations and reading lists, linked to the abstracts that cite a work'], ['Book of Abstracts as pages', '#ernc26', 'ERNC26: searchable, every abstract next to its talk', ' data-act="conftab" data-arg="book"'],
      ['Books of Abstracts on the page', '#ernc25', 'ERNC21 to ERNC26 as embedded PDFs, with a search by page', ' data-act="pasttab" data-arg="ernc25|book"'],
      ['ERN Live', '#live', 'The Twitch stream with programme and chat'],
      ['Teaching library', '#lectures', 'Single teaching units for any seminar, and the autumn lecture series for a whole course'],
      ['A teaching unit', '#l-' + LECT[0].id, 'Student materials and a teacher guide, with a sample package of files that can be downloaded for real'],
      ['Course guide for teachers', '#course', 'How the autumn series runs as a course: workload, essay, credits in other systems, learning platform'],
      ['Propose a lecture', '#propose', 'A form for members and outside speakers. The admin area follows a proposal to a published unit.'],
      ['A page for every autumn lecture', '#lec-1', 'One permanent address from upcoming to live, recording and teaching unit'],
      ['My lectures', '#mylectures', 'For speakers: the state of a proposal, the feedback, and the upload of teaching materials for review'],
      ['Saved library and agenda', '#saved', 'Publications, talks and searches to come back to, apart from the talks you plan to attend'],
      ['Publication pages', '#p-' + STARTERS[0].id.slice(2), 'Every publication has its own page and a link to share'],
      ['Events and deadlines', '#events', 'One calendar in your own time zone'],
      ['My submission', '#submission', 'Drafts, preview, a reference number and a page to follow the review, without an account'],
      ['Member area', '#members', 'A personal overview first, then new journal articles and the esports press, a directory with filters and researcher pages, notices and the own profile'],
      ['Admin area', '#admin', 'Starts with “Needs attention”: what to do today, and who owns it. Then members and rights, conferences, database, teaching, content'],
      ['ERNC27 in one place', '#ernc27', 'Call, submission, programme, registration at NC State and sponsors', ' data-act="c27tab" data-arg="submit"'],
      ['Programme', '#ernc26', 'ERNC26 by day, session and hall, with a personal agenda', ' data-act="conftab" data-arg="prog"'],
      ['Discord', '#members', 'The Discord name in the profile, the directory and the application, and what an ERN bot can do', ' data-act="memgo" data-arg="overview"'],
      ['Online application', '#join', 'Takes the place of the Google Form'], ['Member figures', '#about', 'Numbers and map from the member list', ' data-act="gosec" data-arg="about|sec-members"'],
      ['Ernie', '#home', 'The assistant with the owl, bottom right on every page', ' data-act="askopen"']
    ];
    var built = [
      ['Starter reading list', '#database', 'A first reading list for newcomers, one of the lists in the database', ' data-act="dbstarter"'],
      ['Saved searches', '#saved', 'Save a search in the database and get an e-mail when new entries match'], ['BibTeX export', '#database', 'Citations for reference managers'],
      ['Availability in the profile', '#members', 'Members say whether they review, mentor or answer media enquiries']
    ];
    var later = [
      ['Jobs and opportunities board', '', 'DiGRA, ECSS, the Academy of Management and ICA each run a job board. Here it could also carry PhD positions, calls for papers and funding.'],
      ['Spaces for groups and chapters', '', 'AOM gives each of its 26 divisions a space in its member community, ICA has 34 divisions and interest groups, DiGRA has local chapters.'],
      ['Mentoring for early-career members', '', 'ECSS runs a mentoring programme, ICA a committee for students and early-career scholars.'],
      ['Awards and travel grants', '', 'ECSS and ICA reserve awards and grants for their members.'],
      ['Webinars and methods training', '', 'ECSS offers its members free webinars, AOM a methods training series.'],
      ['Teaching shelf', '', 'AOM keeps a resource library for teaching: syllabi, reading lists and slides shared by members.'],
      ['Citable talks', '', 'Underline and Cassyni give every recorded talk a DOI, and Underline links the talk to its paper, poster and slides.'],
      ['Deposits by members', '', 'On Knowledge Commons every deposit receives a DOI: preprints, posters, slides, data.'],
      ['Author pages for everyone in the database', '', 'Members have a researcher page here. DiGRA has “Find a Researcher” for all authors: a page per author would list abstracts, talks and publications in one place.'],
      ['Filters by method and study type', '', 'Consensus filters by study type. Worth adding once the records of the database carry this reliably.'],
      ['More from Ernie', '', 'Among research assistants, Consensus filters answers by study type and year and keeps a personal library, and Elicit pulls study data into tables.'],
      ['Research in brief', '', 'Short summaries of new articles in plain language for practitioners.'],
      ['General Assembly online', '', 'Registration, documents and votes in the member area.']
    ];
    var steps = [
      ['Search', 'Type “mental health” into the search field on the home page.', '#home', 'To the home page'],
      ['Submit an abstract', 'Start a submission, save it as a draft, come back, preview and submit it. Then open your submission page.', '#ernc27', 'To the submission form', ' data-act="c27tab" data-arg="submit"'],
      ['Prepare a class', 'Open a teaching unit, choose a class plan in the teacher guide and get the materials.', '#l-' + LECT[0].id, 'To a teaching unit'],
      ['Sign in', 'Enter the member area as a demo member. Your overview comes first. New articles and the press are one tab away.', '#members', 'To the member area'],
      ['Ask Ernie', 'Put a question to Ernie, the assistant, for example about integrity.', '#home', 'Open Ernie', ' data-act="askopen"']
    ];
    return liveBar() + pageHero({ title: 'About this mockup', text: 'A clickable sketch of the ERN platform: the current website with the new parts built in.' }) +
      '<div class="container-ern content-wrap x-stack" style="gap:3rem">' +
      '<div><h2 style="font-size:1.5rem;margin-bottom:.5rem">How to read it</h2><ul class="x-plain x-lead"><li>Everything can be clicked. The data are samples, most of them real: the abstracts of ERNC26, ' + D.pubs.length + ' publications, the latest headlines.</li>' +
      '<li>Nothing is saved or sent. A reload starts from the beginning.</li><li>Whatever belongs to the mockup itself carries the dark tag <span class="x-demo-tag">Mockup</span>.</li>' +
      '<li>The switch <button type="button" class="x-link" data-act="mark">Mark what is new</button> at the top shows which parts are new <span class="x-mark" style="margin-left:.125rem">New</span> and which are suggestions <span class="x-mark x-idea" style="margin-left:.125rem">Idea</span>. Unmarked parts are on the current website.</li></ul></div>' +
      '<div><h2 style="font-size:1.5rem;margin-bottom:1.25rem">Five things to try</h2><div class="x-grid4" style="grid-template-columns:repeat(auto-fit,minmax(min(13rem,100%),1fr));gap:1rem">' + steps.map(function (s, i) {
        return '<div class="x-card" style="display:flex;flex-direction:column;padding:1.25rem"><span class="x-small x-muted">' + (i + 1) + ' of 5</span><h3 style="margin:.125rem 0 .5rem">' + s[0] + '</h3><p class="x-small" style="flex:1">' + s[1] + '</p><a class="action-link" href="' + s[2] + '"' + (s[4] || '') + '>' + s[3] + ' ' + ic('arrow', 'x-ic-sm') + '</a></div>';
      }).join('') + '</div></div>' +
      '<div><h2 style="font-size:1.5rem;margin-bottom:1.25rem">What is in it</h2><div class="x-guide-cols">' +
      '<div class="x-card"><h3>From the current website</h3><ul class="x-plain">' + existing.map(li).join('') + '</ul></div>' +
      '<div class="x-card"><h3>New<span class="x-mark">New</span></h3><ul class="x-plain">' + fresh.map(li).join('') + '</ul></div>' +
      '<div class="x-card"><h3>Suggestions, built in<span class="x-mark x-idea">Idea</span></h3><ul class="x-plain">' + built.map(li).join('') + '</ul></div></div></div>' +
      '<div><h2 style="font-size:1.5rem;margin-bottom:.5rem">What similar platforms offer and this one does not yet</h2><p class="x-muted x-lead">Not built here. From a comparison on 10 November 2026 with DiGRA, ECSS, the Academy of Management, ICA, HEVGA, Knowledge Commons, Underline, Cassyni, Oxford Abstracts and two research assistants, read against the ERN goals and the feedback survey after ERNC22.</p>' +
      '<ul class="x-plain" style="grid-template-columns:repeat(auto-fit,minmax(min(20rem,100%),1fr));gap:1rem 2.5rem;margin-top:1.25rem">' + later.map(li).join('') + '</ul></div>' +
      '<div><h2 style="font-size:1.5rem;margin-bottom:.5rem">Decisions the mockup leaves open</h2><ul class="x-plain x-lead"><li>Whether the platform replaces the database at papers.esportsresearch.net or takes over its entries.</li><li>Who curates the database, and who may suggest entries.</li>' +
      '<li>Who builds and runs the ERN bot on Discord.</li><li>Whether a university pays for the link between the lecture series and its learning platform, and who supports it.</li><li>Who forms the lecture team, and which permissions speakers give for recording and slides.</li><li>Whether Ernie answers everyone or members only. Every question costs a little.</li><li>The privacy policy has to cover accounts, submissions and the assistant before the launch.</li><li>How the running costs are covered. DiGRA and ECSS charge their members a yearly fee.</li></ul></div></div>';
  }
