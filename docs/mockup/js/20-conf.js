
  /* ---------- conferences ---------- */
  function confLinks(c) {
    if (c.html) {
      return '<a href="#ernc26" data-act="conftab" data-arg="summary" class="badge-circle">Summary</a>' +
        '<a href="#ernc26" data-act="conftab" data-arg="book" class="badge-circle">Abstracts' + mk() + '</a>' +
        '<a href="#ernc26" data-act="confvideos" class="badge-circle">Videos' + mk() + '</a>';
    }
    return '<a href="#' + c.id + '" data-act="pasttab" data-arg="' + c.id + '|summary" class="badge-circle">Summary</a>' +
      '<a href="#' + c.id + '" data-act="pasttab" data-arg="' + c.id + '|book" class="badge-circle">Abstracts (PDF)' + mk() + '</a>' + ext(c.videos, 'Videos', 'badge-circle');
  }
  function conferences() {
    var c = D.ernc27;
    var past = D.confs.map(function (x) {
      return '<div class="action-card"><div class="action-icon">' + ic('cal') + '</div><h3>' + esc(x.name) + '</h3><p style="margin-bottom:.5rem">' + esc(x.theme) + '</p><p class="ern-conf-meta">' + esc(x.place) + ' · ' + esc(x.dates) + '</p><div style="display:flex;flex-wrap:wrap;gap:.5rem">' + confLinks(x) + '</div></div>';
    }).join('');
    return liveBar() +
      '<section class="hero-section x-hero-conf"><div class="container-ern"><div style="max-width:64rem">' +
      '<h1 tabindex="-1" style="margin-bottom:1.5rem">' + esc(c.title) + '</h1>' +
      '<div class="conference-info" style="margin-bottom:1.5rem">' + info('cal', c.dates) + info('clock', c.mode) + info('globe', c.place) + '</div>' +
      '<div class="conference-tags" style="margin-bottom:1rem"><a href="#ernc27" data-act="c27tab" data-arg="call" class="badge-circle">Call for Abstracts</a><a href="#ernc27" data-act="c27tab" data-arg="submit" class="badge-circle">Submit an Abstract</a><a href="#ernc27" data-act="c27tab" data-arg="programme" class="badge-circle">Programme</a><a href="#ernc27" data-act="c27tab" data-arg="sponsors" class="badge-circle">Sponsors</a>' +
      '</div>' +
      '<ul class="ern-key-dates">' + c.keydates.map(function (d) { return '<li class="conference-info-item">' + ic('cal') + '<span><strong>' + esc(d[0]) + ':</strong> ' + esc(d[1]) + '</span></li>'; }).join('') + '</ul>' +
      '<div class="hero-buttons" style="margin-top:2rem"><button type="button" class="btn btn-primary btn-lg" data-act="scrollto" data-arg="sec-archive">Explore Past Conferences</button></div>' +
      '</div></div></section>' +
      '<section class="section-spacing bg-background"><div class="container-ern"><div class="about-section-with-image"><div>' +
      '<img src="img/ernc27.png" alt="ERNC27" style="height:2.5rem;width:auto;margin-bottom:1.25rem"><h2>' + esc(c.theme) + '</h2>' +
      '<div style="color:hsl(var(--muted-foreground));line-height:1.75">' + paras(c.intro) + '<ul style="padding-left:1.25rem;margin:0 0 1.5rem;display:grid;gap:.5rem">' + c.questions.map(function (q) { return '<li>' + esc(q) + '</li>'; }).join('') + '</ul></div>' +
      '<a href="#ernc27" data-act="c27tab" data-arg="call" class="btn btn-outline">Read the Call for Abstracts</a></div>' +
      '<div><img src="img/hall.jpg" alt="A session in the Main Hall at ERNC26" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></div></div></div></section>' +
      '<section class="x-sponsorband"><div class="container-ern">' + sponsorStrip() + '</div></section>' +
      '<section id="sec-archive" class="section-spacing bg-secondary-light"><div class="container-ern"><div class="section-header"><h2>Past ERN Conferences</h2><p>Explore research and recordings from past ERN conferences</p></div><div class="quick-actions-grid">' + past + '</div></div></section>';
  }

  /* ---------- sponsors: space for logos ---------- */
  var TIERS = [['Main sponsor', 1, 'x-sp-l'], ['Sponsors', 4, 'x-sp-m'], ['Supporters', 6, 'x-sp-s']];
  function logoSpace(cls, label) { return '<span class="x-sp ' + cls + '">' + (label || 'Logo') + '</span>'; }
  function sponsorStrip() {
    return '<div class="x-sponsorstrip"><span class="x-small x-muted">ERNC27 is hosted by</span><span class="x-sp x-sp-host">NC State University</span><span class="x-small x-muted">with' + mk() + '</span><span class="x-sp x-sp-host">Visit Raleigh</span>' +
      logoSpace('x-sp-m') + logoSpace('x-sp-m') + '<a class="x-small" href="#ernc27" data-act="c27tab" data-arg="sponsors">All sponsors</a></div>';
  }
  function sponsorsTab() {
    return '<div class="x-cols"><div class="x-stack" style="gap:2rem"><div><h2 style="font-size:1.5rem;margin-bottom:.5rem">Host</h2><div class="x-sprow"><span class="x-sp x-sp-l x-sp-host">NC State University</span></div></div>' +
      '<div><h2 style="font-size:1.5rem;margin-bottom:.5rem">Partner</h2><div class="x-sprow"><span class="x-sp x-sp-l x-sp-host">Visit Raleigh</span></div></div>' +
      TIERS.map(function (t) {
        var tiles = ''; for (var k = 0; k < t[1]; k++) tiles += logoSpace(t[2], 'Logo space');
        return '<div><h2 style="font-size:1.5rem;margin-bottom:.5rem">' + t[0] + '</h2><div class="x-sprow">' + tiles + '</div></div>';
      }).join('') +
      demo('<p>NC State University and Visit Raleigh are shown by name, their logos would stand here. The grey fields are spaces for further logos. The levels and their sizes are a suggestion.</p>') + '</div>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>Where the logos appear</h3><ul class="x-small x-botlist"><li>On this page, by level</li><li>In the band on the conference pages</li><li>In the programme and the Book of Abstracts</li><li>On ERN Live during the conference</li></ul><p class="x-small x-muted" style="margin:0">Each logo links to the website of the sponsor.</p></div>' +
      '<div class="x-card"><h3>Become a sponsor</h3><p class="x-small x-muted">Support the conference and reach esports researchers from around the world.</p><p class="x-small" style="margin:0">Write to <strong>ernc@esportsresearch.net</strong></p></div></aside></div>';
  }

  /* ---------- ERNC27: call, submission, programme, sponsors ---------- */
  var STRANDS = ['Esports Research', 'Esports Competition', 'Esports Community'];
  var FORMATS = ['Presentation, in person', 'Presentation, online', 'Poster'];
  var SECTIONS = ['Introduction', 'Purpose', 'Methods', 'Results', 'Practical Implications', 'Conclusion'];
  function words(f) { var n = 0; Array.prototype.forEach.call(f.querySelectorAll('[data-input=subw]'), function (el) { var v = el.value.trim(); if (v) n += v.split(/\s+/).length; }); return n; }
  var SUBIDS = ['sub-title', 'sub-format', 'sub-strand', 'sub-name', 'sub-mail', 'sub-aff', 'sub-co', 'sub-s0', 'sub-s1', 'sub-s2', 'sub-s3', 'sub-s4', 'sub-s5', 'sub-refs', 'sub-kw', 'sub-d0', 'sub-d1', 'sub-d2', 'sub-reg'];
  function todayStamp() { return new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) + ', ' + nowTime(); }
  function nowTime() { var d = new Date(); return ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2); }
  function subWords() { var n = 0; ['sub-s0', 'sub-s1', 'sub-s2', 'sub-s3', 'sub-s4', 'sub-s5', 'sub-refs'].forEach(function (k) { var v = String(S.sub.vals[k] || '').trim(); if (v) n += v.split(/\s+/).length; }); return n; }
  function fillSub() {
    var f = document.querySelector('form[data-form=submit]'); if (!f) return;
    SUBIDS.forEach(function (k) { var el = document.getElementById(k), v = S.sub.vals[k]; if (!el || v == null) return; if (el.type === 'checkbox') el.checked = !!v; else el.value = v; });
    var c = document.getElementById('sub-count'), n = subWords(); if (c) c.innerHTML = '<strong>' + n + '</strong> of 500 words' + (n > 500 ? ', ' + (n - 500) + ' too many' : '');
  }
  function submitTab() {
    var c = D.ernc27, v = S.sub.vals, rev = S.sub.revising;
    var steps = '<div class="x-card"><h3>What happens next</h3><ol class="x-steps"><li>You receive a confirmation by e-mail, with a link to your submission page.</li><li>The Scientific Committee reviews every abstract.</li><li>Decisions go to the corresponding author on 15 March 2027.</li><li>Revised abstracts are due on 31 March 2027.</li><li>At least one author registers by 1 September 2027.</li></ol></div>' +
      '<div class="x-card"><h3>No account needed</h3><p class="x-small x-muted" style="margin:0">You submit with your e-mail address. Drafts and your submission page open through a link that we send you. Members who are signed in also find both in their overview.</p></div>' +
      '<div class="x-card"><h3>Panels and workshops</h3><p class="x-small x-muted" style="margin:0">These are proposed with template B by e-mail to <strong>ernc@esportsresearch.net</strong>, not with this form.</p></div>';
    var wrap = function (main) { return '<div class="x-cols"><div class="x-stack">' + main + '</div><aside class="x-stack" style="gap:1rem">' + steps + '</aside></div>'; };
    var lost = S.sub.error ? '<div class="x-card x-notice x-bad" role="alert" id="sub-error">' + ic('x') + '<div><strong>' + S.sub.error + ' did not work: there is no connection</strong><p class="x-small" style="margin:.125rem 0 .5rem">Nothing is lost. Your text is still here. Try again when you are back online.</p><button type="button" class="btn btn-outline x-btn-sm" data-act="subretry">Try again</button></div></div>' : '';
    if (S.sub.stage === 'done') {
      var last = S.subs[S.subs.length - 1];
      return wrap('<div class="x-card"><div class="x-success">' + ic('check') + '<div><h3>' + (last.revised ? 'Thank you, your revision is received' : 'Thank you, your abstract is submitted') + '</h3><p style="margin-bottom:.5rem"><strong>' + esc(last.title) + '</strong></p><p class="x-small x-muted" style="margin:0">' + esc(last.format) + ' · ' + esc(last.strand) + ' · Reference number ' + last.no + '</p></div></div>' +
        '<p class="x-small" style="margin:1rem 0 .75rem">We sent a confirmation to <strong>' + esc(last.mail) + '</strong>. It holds the link to your submission page, where you follow the review.</p><a href="#submission" class="btn btn-primary x-btn-sm">Open my submission page</a></div>' +
        demo('<p>Nothing was sent. The button stands for the link in the e-mail.</p><button type="button" class="x-link" data-act="subreset">Start a new submission</button>'));
    }
    if (S.sub.stage === 'saved') {
      return wrap('<div class="x-card"><div class="x-success">' + ic('check') + '<div><h3>Your draft is saved</h3><p class="x-small" style="margin:0">Saved at ' + S.sub.savedAt + '. We sent a link to <strong>' + esc(v['sub-mail']) + '</strong>. You can close this page. The link brings you back to your draft until the deadline on 4 January 2027.</p></div></div></div>' +
        demo('<p>Nothing was sent. Go to another page if you like, then come back: the button stands for the link in the e-mail.</p><button type="button" class="btn btn-outline x-btn-sm" data-act="subresume">Open the link from the e-mail</button>'));
    }
    if (S.sub.stage === 'preview') {
      var sec = SECTIONS.map(function (h, n) { return '<h4 class="x-h4">' + h + '</h4><p>' + esc(v['sub-s' + n]) + '</p>'; }).join('');
      return wrap(lost + '<div class="x-card"><p class="x-small x-muted" style="margin-bottom:.75rem">Check your abstract. This is how the reviewers will see it.</p><span class="x-item-meta">' + esc(v['sub-format']) + ' · ' + esc(v['sub-strand']) + '</span><h3 style="font-size:1.375rem;margin:.25rem 0">' + esc(v['sub-title']) + '</h3>' +
        '<p class="x-muted">' + esc(v['sub-name']) + ', ' + esc(v['sub-aff']) + (v['sub-co'] ? '; ' + esc(String(v['sub-co']).replace(/\n+/g, '; ')) : '') + '</p>' + sec +
        (v['sub-refs'] ? '<h4 class="x-h4">References</h4><p class="x-small">' + esc(v['sub-refs']) + '</p>' : '') + '<p class="x-small"><strong>Keywords:</strong> ' + esc(v['sub-kw']) + '</p><p class="x-small x-muted">' + subWords() + ' of 500 words</p>' +
        '<div class="x-row" style="margin-top:1rem"><button type="button" class="btn btn-primary" data-act="subfinal">' + (rev ? 'Submit the revision' : 'Submit now') + '</button><button type="button" class="btn btn-outline" data-act="subedit">Back to edit</button></div></div>');
    }
    return wrap(
      demo('<p><strong>Nothing you enter here is sent.</strong> To submit to ERNC27 today, use the form on the current website.</p>' + ext(c.submit, 'Open the current submission form') +
        '<p style="margin-top:.5rem">Try a failure: <button type="button" class="x-chip" data-act="suboffline" aria-pressed="' + S.sub.offline + '">Pretend the connection is lost</button></p>') + lost +
      (rev ? '<div class="x-card x-notice" role="status">' + ic('pen') + '<div><strong>You are revising ' + rev + '</strong><p class="x-small" style="margin:.125rem 0 0">Change the text where the editor asked for it, then submit the revision.</p></div></div>' : '') +
      '<form class="x-card x-form" data-form="submit" novalidate><p class="x-small x-muted" style="margin:0">Abstracts are due on Monday 4 January 2027. Fields marked with <span class="x-req">*</span> are required.' + (S.sub.savedAt ? ' <strong id="sub-savedat">Draft, last saved at ' + S.sub.savedAt + '.</strong>' : '') + '</p>' +
      '<h3 class="x-formhead">1. Your contribution</h3>' + fld('sub-title', 'Title', { req: 1 }) +
      '<div class="x-two">' + fld('sub-format', 'Preferred format', { req: 1, opts: FORMATS }) + fld('sub-strand', 'Strand', { req: 1, opts: STRANDS, help: 'If none fits, choose the closest one.' }) + '</div>' +
      '<h3 class="x-formhead">2. Authors</h3><div class="x-two">' + fld('sub-name', 'Corresponding author', { req: 1 }) + fld('sub-mail', 'E-mail address', { req: 1, type: 'email', ph: 'you@university.edu', help: 'Drafts, the confirmation and the decision go to this address.' }) + '</div>' +
      fld('sub-aff', 'University or organisation', { req: 1 }) + fld('sub-co', 'Co-authors', { area: 1, rows: 2, help: 'One per line: name, university or organisation.' }) +
      '<h3 class="x-formhead">3. Abstract</h3><p class="x-small x-muted" style="margin:0">Six headings, at most 500 words in total, including references. In English.</p>' +
      SECTIONS.map(function (h, n) { return fld('sub-s' + n, h, { req: 1, area: 1, rows: 3, di: 'subw' }); }).join('') +
      fld('sub-refs', 'References', { area: 1, rows: 3, di: 'subw', help: 'APA 7th edition. They count towards the 500 words.' }) +
      '<p class="x-small" id="sub-count" aria-live="polite" style="margin:0"><strong>0</strong> of 500 words</p>' +
      fld('sub-kw', 'Keywords', { req: 1, help: 'Three to five, separated by commas.' }) +
      '<h3 class="x-formhead">4. Presenting</h3><fieldset class="x-fieldset"><legend>Days on which you cannot present <small>(optional)</small></legend>' +
      ['Tuesday 9 November', 'Wednesday 10 November', 'Thursday 11 November'].map(function (d, n) { return '<label class="x-check" for="sub-d' + n + '"><input type="checkbox" id="sub-d' + n + '"><span>' + d + '</span></label>'; }).join('') + '</fieldset>' +
      '<div><label class="x-check" for="sub-reg"><input type="checkbox" id="sub-reg" name="sub-reg" aria-describedby="sub-reg-err"><span>If the abstract is accepted, at least one author registers for the conference. <span class="x-req" aria-hidden="true">*</span></span></label><span class="x-err" id="sub-reg-err" hidden></span></div>' +
      '<div class="x-row"><button class="btn btn-primary" type="submit">' + (rev ? 'Preview the revision' : 'Preview and submit') + '</button>' + (rev ? '' : '<button type="button" class="btn btn-outline" data-act="subdraft">Save as a draft</button>') + '</div></form>');
  }
  /* the page an author opens from the link in the confirmation e-mail */
  function submission() {
    var x = S.subs[S.subs.length - 1], head = pageHero({ crumbs: [['Conferences', '#conferences'], ['ERNC27', '#ernc27'], ['My submission']], cls: 'x-hero-conf', title: 'My submission' + mk(), text: 'ERNC27, 9 to 11 November 2027' });
    if (!x) return liveBar() + head + '<div class="container-ern content-wrap"><div class="x-stack" style="max-width:36rem"><p>This page opens from the link in your confirmation e-mail. You have not submitted an abstract in this visit.</p><div><a href="#ernc27" data-act="c27tab" data-arg="submit" class="btn btn-primary">To the submission form</a></div></div></div>';
    var d = S.adm.dec[x.no], sent = d && d.sent, view = sent ? (d.res === 'Acceptance' ? 'accepted' : d.res === 'Rejection' ? 'rejected' : 'revise') : S.subView;
    if (x.revised && view === 'revise') view = 'revised';
    var fb = sent && d.fb ? d.fb : 'The results section reports anticipated results only. Please say so in the text and shorten the references.';
    var st = { review: ['In review', 'The Scientific Committee is reading your abstract. You do not need to do anything.'], revise: ['Revisions requested', 'Your abstract is accepted if you make the changes below. The revised abstract is due within two weeks.'], revised: ['Revision received', 'Thank you. The editor checks the revised abstract. You hear from us by e-mail.'], accepted: ['Accepted', 'Congratulations. Your abstract is part of the ERNC27 programme.'], rejected: ['Not accepted', 'We are sorry. Your abstract could not be accepted this time.'] }[view];
    return liveBar() + head + '<div class="container-ern content-wrap"><div class="x-cols"><div class="x-stack">' +
      (sent ? '' : demo('<p>Show this page as the author would see it later:</p><div class="x-chips">' + [['review', 'In review'], ['revise', 'Revisions requested'], ['accepted', 'Accepted']].map(function (o) { return chip(o[1], 'subview', o[0], S.subView === o[0]); }).join('') + '</div>')) +
      '<div class="x-card"><span class="x-item-meta">Reference number ' + x.no + ' · ' + esc(x.format) + ' · ' + esc(x.strand) + '</span><h2 style="font-size:1.375rem;margin:.25rem 0 1rem">' + esc(x.title) + '</h2>' +
      '<div class="x-notice" style="padding:0" role="status">' + ic(view === 'review' || view === 'revised' ? 'clock' : view === 'rejected' ? 'x' : view === 'revise' ? 'pen' : 'check') + '<div><strong>' + st[0] + '</strong><p class="x-small" style="margin:.125rem 0 0">' + st[1] + '</p></div></div>' +
      (view === 'revise' || view === 'rejected' || (view === 'accepted' && sent && d.fb) ? '<h3 style="font-size:1.0625rem;margin:1.25rem 0 .375rem">Feedback from the editor</h3><p class="x-cite" style="user-select:text">' + esc(fb) + '</p>' : '') +
      (view === 'revise' ? '<div class="x-row" style="margin-top:1rem"><button type="button" class="btn btn-primary" data-act="subrevise">Revise the abstract</button></div>' : '') +
      (view === 'accepted' ? '<div class="x-row" style="margin-top:1rem"><a href="#ernc27" data-act="c27tab" data-arg="registration" class="btn btn-primary x-btn-sm">How to register</a></div>' : '') + '</div>' +
      '<ol class="x-timeline"><li><strong>Submitted</strong><span>Confirmation sent to ' + esc(x.mail) + '</span></li><li class="' + (view === 'review' ? 'x-wait' : '') + '"><strong>Review by the Scientific Committee</strong><span>Two reviewers, then the final editor</span></li><li class="' + (view === 'review' ? 'x-todo' : view === 'revise' || view === 'revised' ? 'x-wait' : '') + '"><strong>Decision</strong><span>' + (view === 'review' ? 'On 15 March 2027, by e-mail and on this page' : st[0]) + '</span></li><li class="' + (view === 'accepted' ? 'x-wait' : 'x-todo') + '"><strong>Registration</strong><span>At least one author registers by 1 September 2027</span></li></ol></div>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>Questions?</h3><p class="x-small" style="margin:0">Write to <strong>ernc@esportsresearch.net</strong> and name your reference number.</p></div><div class="x-card"><h3>Lost the link?</h3><p class="x-small x-muted" style="margin:0">Enter your e-mail address on the submission form and we send it again.</p></div></aside></div></div>';
  }
  function registration27() {
    return '<div class="x-cols"><div class="x-stack"><div class="x-card"><h2 style="font-size:1.375rem;margin-bottom:.75rem">Registration is handled by NC State University</h2><dl class="x-facts"><div><dt>Opens</dt><dd>Saturday 3 April 2027</dd></div><div><dt>Fees</dt><dd>Announced when registration opens, including a reduced student rate</dd></div><div><dt>Presenters</dt><dd>At least one author of every contribution registers by Wednesday 1 September 2027</dd></div><div><dt>Online presenters</dt><dd>Registration is required as well</dd></div></dl>' +
      '<div class="x-row" style="margin-top:1.25rem"><button type="button" class="btn btn-primary" disabled aria-disabled="true">Register at NC State</button><span class="x-small x-muted">The button opens the registration page of NC State University from 3 April 2027.</span></div></div>' +
      '<div class="x-card"><h3>What happens where</h3><dl class="x-facts x-small"><div><dt>On this site</dt><dd>Call for Abstracts, submission, review, programme and your agenda</dd></div><div><dt>At NC State</dt><dd>Registration, payment and your receipt</dd></div></dl></div></div>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>Questions about registration</h3><p class="x-small" style="margin:0">Write to <strong>ernc@esportsresearch.net</strong>. We pass questions on payment to the team at NC State.</p></div><div class="x-card"><h3>Remind me</h3><p class="x-small x-muted">The opening day is in the events calendar.</p><a href="#events" class="btn btn-outline x-btn-sm">Open the calendar</a></div></aside></div>';
  }
  function programme27() {
    var days = [['Monday 8 November', 'Pre-conference workshops', 'Half-day, hands-on sessions for 20 to 30 participants.'], ['Tuesday 9 November', 'Conference day 1', 'Themed parallel sessions with 15-minute presentations and 5 minutes of discussion.'],
      ['Wednesday 10 November', 'Conference day 2', 'Parallel sessions and the poster session, with the authors present.'], ['Thursday 11 November', 'Conference day 3', 'Parallel sessions.']];
    return '<div class="x-cols"><div class="x-stack"><p class="x-muted x-lead" style="margin:0">The programme is published here after the review, in spring 2027. This is the frame from the Call for Abstracts.</p><ul class="x-list">' + days.map(function (d) {
      return '<li class="x-item"><div class="x-item-row"><div class="x-item-main"><span class="x-item-meta">' + d[0] + ' 2027</span><span class="x-item-title">' + d[1] + '</span><span class="x-item-by">' + d[2] + '</span></div></div></li>';
    }).join('') + '</ul><p class="x-small x-muted" style="margin:0">Pre-recorded online presentations are published on the ERN YouTube channel and listed in the programme.</p></div>' +
      '<aside class="x-card"><h3>How the programme will look</h3><p class="x-small x-muted">By day and session, with every talk linked to its abstract, and a personal agenda.</p><a href="#ernc26" data-act="conftab" data-arg="prog" class="btn btn-outline x-btn-sm">See the ERNC26 programme</a></aside></div>';
  }
  function ernc27() {
    var c = D.ernc27, tab = S.c27Tab;
    var tabs = [['call', 'Call for Abstracts'], ['submit', 'Submit', null, mk()], ['programme', 'Programme', null, mk()], ['registration', 'Registration', null, mk()], ['sponsors', 'Sponsors', null, mk()]];
    var body = tab === 'submit' ? submitTab() : tab === 'programme' ? programme27() : tab === 'registration' ? registration27() : tab === 'sponsors' ? sponsorsTab() :
      '<div class="x-cols x-side-first"><article class="entry-content x-prose">' + D.pages.cfa + '</article>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>At a glance</h3><dl class="x-facts x-small" style="margin-bottom:1rem">' +
      [['Abstracts due', '4 January 2027'], ['Decisions', '15 March 2027'], ['Registration opens', '3 April 2027'], ['Conference', '9 to 11 November 2027']].map(function (d) { return '<div style="grid-template-columns:8.5rem minmax(0,1fr)"><dt>' + d[0] + '</dt><dd>' + d[1] + '</dd></div>'; }).join('') +
      '</dl><button type="button" class="btn btn-primary x-btn-sm" data-act="c27tab" data-arg="submit">Submit an abstract</button></div>' +
      demo('<p>This is the Call for Abstracts from the current website.</p>') + '</aside></div>';
    return liveBar() + pageHero({
      crumbs: [['Conferences', '#conferences'], ['ERNC27']], cls: 'x-hero-conf', title: 'ERNC27: Think &amp; Do', text: 'Where Research, Competition &amp; Community Converge',
      extra: '<div class="conference-info">' + info('cal', c.dates) + info('globe', c.place) + '</div>'
    }) + '<div class="container-ern x-tabwrap">' + tabsRow('c27tab', tabs, tab, 'ERNC27').replace('class="x-tabs"', 'class="x-tabs x-many"') + '</div>' + (tab === 'call' ? subnav(D.pages.cfaNav) : '') +
      '<div class="container-ern content-wrap x-stack x-aftertabs" role="tabpanel">' + body + '<div class="x-sponsorfoot">' + (tab === 'sponsors' ? '' : sponsorStrip()) + '</div></div>';
  }

  /* ---------- a past conference: summary, the Book of Abstracts on the page, recordings ---------- */
  function bookTab(c, note) {
    return pdfHtml(c) + '<div class="x-row x-between"><span class="x-small x-muted">' + c.pages + ' pages. The search finds the pages on which all your words appear.</span>' + ext(c.pdf, 'Open the PDF in a new tab', 'x-small') + '</div>' + (note || '');
  }
  function confSummary(id) {
    var c = CONF[id], tab = S.pastTab[id] || 'summary';
    var tabs = [[id + '|summary', 'Summary'], [id + '|book', 'Book of Abstracts', null, mk()]];
    var body = tab === 'book'
      ? bookTab(c, demo('<p>This is the published book, shown on the page. The next step for a past conference is the version that ERNC26 has here: every abstract as its own page, next to its talk.</p><a href="#ernc26" data-act="conftab" data-arg="book">See ERNC26</a>'))
      : '<div class="x-cols x-side-first"><article class="entry-content x-prose"><h2>Conference Overview</h2>' + paras(c.text) +
        '<dl class="x-facts" style="font-size:1rem">' + c.facts.map(function (f) { return '<div><dt>' + esc(f[0]) + '</dt><dd>' + esc(f[1]) + '</dd></div>'; }).join('') + '</dl></article>' +
        '<aside class="x-card"><h3>Abstracts and recordings</h3><div class="x-stack" style="gap:.625rem;align-items:flex-start">' +
        '<button type="button" class="btn btn-primary x-btn-sm" data-act="pasttab" data-arg="' + id + '|book">' + ic('book') + 'Read the Book of Abstracts</button>' + ext(c.videos, ic('video') + 'Videos on YouTube', 'btn btn-outline x-btn-sm') + '</div>' +
        '<p class="x-small" style="margin-top:1rem">' + ext(c.live, 'Full summary on the current site') + '</p></aside></div>';
    return liveBar() + pageHero({
      crumbs: [['Conferences', '#conferences'], [c.name]], cls: 'x-hero-conf', title: esc(c.name + ': ' + c.theme),
      extra: '<div class="conference-info">' + info('cal', c.dates) + info('clock', c.mode) + info('globe', c.place) + '</div>'
    }) + '<div class="container-ern content-wrap x-stack">' + tabsRow('pasttab', tabs, id + '|' + tab, c.name) + '<div class="x-stack" role="tabpanel">' + body + '</div></div>';
  }

  /* ---------- ERNC26: the Book of Abstracts as a page ---------- */
  function bookActive() { return !!(S.bookQ || S.bookKw || S.bookVideo || S.bookSaved || S.bookDay !== 'all'); }
  function bookFilter() {
    var ts = toks(S.bookQ), kw = fold(S.bookKw);
    var list = D.abstracts.map(function (a) { return { a: a, s: ts.length ? score(a, ts) : 1 }; }).filter(function (r) {
      var a = r.a;
      if (!r.s) return false;
      if (S.bookVideo && a.video !== 'yes') return false;
      if (S.bookSaved && !S.saved[a.id]) return false;
      if (kw && a.kw.map(fold).indexOf(kw) < 0) return false;
      if (S.bookDay === 'online') return a.fmt === 'online';
      if (S.bookDay !== 'all') return a.fmt !== 'online' && a.s && a.s.day === S.bookDay;
      return true;
    });
    if (ts.length) list.sort(function (x, y) { return y.s - x.s; });
    else if (S.bookDay !== 'all' && S.bookDay !== 'online') list.sort(function (x, y) { return (x.a.s.time + x.a.s.room) < (y.a.s.time + y.a.s.room) ? -1 : 1; });
    return list.map(function (r) { return r.a; });
  }
  function saveBtn(a) {
    var on = !!S.saved[a.id];
    return '<button type="button" class="x-item-side x-save" data-act="save" data-arg="' + a.id + '" aria-pressed="' + on + '" aria-label="' + (on ? 'Remove from my saved talks' : 'Save this talk for later') + '" title="' + (on ? 'Saved. Click to remove.' : 'Save for later') + '">' + ic('save') + '</button>';
  }
  function absItem(a, q) {
    var tags = '';
    if (a.fmt === 'panel') tags += tag('Panel', 1);
    if (a.video === 'yes') tags += tag(PLAY + 'Video');
    else if (a.video === 'soon') tags += tag('Video in preparation', 1);
    return '<li class="x-item"><div class="x-item-row"><a class="x-item-main" href="#' + a.id + '"><span class="x-item-meta">' + esc(sessionLine(a)) + '</span><span class="x-item-title">' + hl(a.title, q) + '</span><span class="x-item-by">' + hl(a.aus, q) + '</span>' + (tags ? '<span class="x-item-tags">' + tags + '</span>' : '') + '</a>' + saveBtn(a) + '</div></li>';
  }
  function bookResults() {
    var list = bookFilter();
    var line = '<p class="x-filterline" aria-live="polite"><span>' + (bookActive() ? list.length + ' of ' + D.abstracts.length + ' entries' : 'All ' + D.abstracts.length + ' entries') + (S.bookKw ? ' with the keyword “' + esc(S.bookKw) + '”' : '') + '</span>' +
      (bookActive() ? '<button type="button" class="x-link" data-act="bookclear">Clear all filters</button>' : '') + '</p>';
    if (list.length) return line + '<ul class="x-list">' + list.map(function (a) { return absItem(a, S.bookQ); }).join('') + '</ul>';
    return line + '<div class="x-empty">' + (S.bookSaved && !savedCount() ? 'You have not saved a talk yet. Use the bookmark on the right of an entry.' : 'Nothing found. Try another word, for example Valorant, integrity or Japan.') + '</div>';
  }
  function bookControls() {
    var days = [['all', 'All days'], ['Tue', 'Tue 15 Sep'], ['Wed', 'Wed 16 Sep'], ['Thu', 'Thu 17 Sep'], ['online', 'Pre-recorded']];
    return days.map(function (d) { return chip(d[1] + ' (' + DAYN[d[0]] + ')', 'bookday', d[0], S.bookDay === d[0]); }).join('') + SEP +
      chip('With video (' + WITHVIDEO + ')', 'bookvideo', '', S.bookVideo) + chip('Saved (' + savedCount() + ')', 'booksaved', '', S.bookSaved, mk('idea'));
  }
  function progBody() {
    var days = [['Tue', 'Tuesday 15 September'], ['Wed', 'Wednesday 16 September'], ['Thu', 'Thursday 17 September']], day = S.progDay;
    var mine = S.progMine, list = D.abstracts.filter(function (a) { return a.s && a.s.day === day && (!mine || S.agenda[a.id]); });
    var sessions = {}; list.forEach(function (a) { (sessions[a.s.no] = sessions[a.s.no] || []).push(a); });
    var out = Object.keys(sessions).sort().map(function (no) {
      var items = sessions[no], halls = {}, from = '99', to = '00';
      items.forEach(function (a) { (halls[a.s.room] = halls[a.s.room] || []).push(a); if (a.s.time < from) from = a.s.time; if (a.s.end > to) to = a.s.end; });
      return '<section class="x-card x-session"><div class="x-row x-between" style="margin-bottom:1rem"><h3 style="margin:0">Parallel session ' + no + '</h3><span class="x-small x-muted">' + from + ' to ' + to + '</span></div><div class="x-halls">' +
        Object.keys(halls).sort().map(function (r) {
          var hs = halls[r].sort(function (x, y) { return x.s.time < y.s.time ? -1 : 1; });
          return '<div><h4 class="x-h4" style="margin-top:0">' + esc(hs[0].s.hall) + '</h4><ul class="x-slots">' + hs.map(function (a) {
            var on = !!S.agenda[a.id];
            return '<li><time>' + a.s.time + '</time><div><a href="#' + a.id + '">' + esc(a.title) + '</a><span class="x-small x-muted">' + esc(a.aus) + (a.fmt === 'panel' ? ' · Panel' : '') + '</span></div>' +
              '<button type="button" class="x-save x-agbtn" data-act="agenda" data-arg="' + a.id + '" aria-pressed="' + on + '" aria-label="' + (on ? 'Remove from my agenda: ' : 'Add to my agenda: ') + esc(a.title) + '" title="' + (on ? 'In my agenda' : 'Add to my agenda') + '">' + ic(on ? 'check' : 'plus') + '</button></li>';
          }).join('') + '</ul></div>';
        }).join('') + '</div></section>';
    }).join('');
    var mineAll = D.abstracts.filter(function (a) { return a.s && a.s.day === day && S.agenda[a.id]; }), clash = [];
    mineAll.forEach(function (x, n) { mineAll.slice(n + 1).forEach(function (y) { if (x.s.time < y.s.end && y.s.time < x.s.end) clash.push([x, y]); }); });
    return '<div class="x-chips" role="group" aria-label="Day">' + days.map(function (d) { return chip(d[1], 'progday', d[0], day === d[0]); }).join('') + SEP + chip('My agenda (' + agendaCount() + ')', 'progmine', '', mine, mk()) + '</div>' +
      (agendaCount() ? '<div class="x-row x-between x-small"><span class="x-muted">Your agenda holds the talks you plan to attend. It is separate from your saved library.' + whereSaved() + '</span><button type="button" class="btn btn-outline x-btn-sm" data-act="toast" data-arg="Not built in this mockup: a calendar file with your ' + agendaCount() + ' talks would be saved, in your own time zone.">Add my agenda to my calendar</button></div>' : '') +
      clash.map(function (c) { return '<div class="x-card x-notice" role="status">' + ic('clock') + '<div><strong>Two talks in your agenda overlap</strong><p class="x-small" style="margin:.125rem 0 0">' + esc(c[0].title) + ' (' + c[0].s.time + ', ' + esc(c[0].s.hall) + ') and ' + esc(c[1].title) + ' (' + c[1].s.time + ', ' + esc(c[1].s.hall) + ').</p></div></div>'; }).join('') +
      (out || '<div class="x-empty">' + (mine ? 'Nothing from this day is in your agenda yet. Use the plus next to a talk.' : 'No sessions on this day.') + '</div>') +
      demo('<p>The talks of the parallel sessions, with the real times and halls from the Book of Abstracts. Keynotes, panels and breaks would be added. The nine pre-recorded talks have no slot and are in the list of abstracts.</p>');
  }
  function ernc26Body() {
    var c = CONF.ernc26;
    if (S.confTab === 'prog') return progBody();
    if (S.confTab === 'pdf') return bookTab(c, demo('<p>This is the printed book. The tab “Abstracts and talks” holds the same abstracts as pages, with search across the full text and the talks next to them.</p>'));
    if (S.confTab === 'summary') {
      return '<div class="x-cols x-side-first"><article class="entry-content x-prose">' + D.pages.ernc26 + '</article>' +
        '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>Abstracts and recordings</h3><div class="x-stack" style="gap:.625rem;align-items:flex-start">' +
        '<button type="button" class="btn btn-primary x-btn-sm" data-act="conftab" data-arg="book">' + ic('search') + 'Search the abstracts</button>' +
        '<button type="button" class="btn btn-outline x-btn-sm" data-act="conftab" data-arg="pdf">' + ic('book') + 'Read the printed book</button>' + ext(c.videos, ic('video') + 'Videos on YouTube', 'btn btn-outline x-btn-sm') + '</div></div></aside></div>';
    }
    return '<p class="x-muted x-lead" style="margin:0">All ' + D.abstracts.length + ' entries of the printed book. Open an entry to read the abstract, watch the talk and follow its references.</p>' +
      '<div class="x-search">' + ic('search') + '<label class="x-vh" for="book-q">Search the Book of Abstracts</label><input id="book-q" type="search" data-input="bookq" value="' + esc(S.bookQ) + '" placeholder="Search titles, authors, keywords and the full text" autocomplete="off"></div>' +
      '<div class="x-chips" id="book-controls" role="group" aria-label="Filter the entries">' + bookControls() + '</div><div id="book-results" class="x-stack" style="gap:.875rem">' + bookResults() + '</div>';
  }
  function ernc26() {
    var c = CONF.ernc26;
    var tabs = [['book', 'Abstracts<span class="x-wide"> and talks</span>', D.abstracts.length, mk()], ['prog', 'Programme', null, mk()], ['summary', 'Summary'], ['pdf', 'Book as PDF', null, mk()]];
    return liveBar() + pageHero({
      crumbs: [['Conferences', '#conferences'], ['ERNC26']], cls: 'x-hero-conf', title: 'ERNC26: Bridging Gaming Heritage and Esports Futures',
      extra: '<div class="conference-info">' + info('cal', c.dates) + info('globe', c.place) + info('users', '163 participants from 29 countries') + '</div>'
    }) + '<div class="container-ern content-wrap x-stack">' + tabsRow('conftab', tabs, S.confTab, 'ERNC26') + '<div id="conf-body" class="x-stack" role="tabpanel">' + ernc26Body() + '</div></div>';
  }

  /* ---------- one abstract with its talk ---------- */
  function videoBlock(a) {
    if (a.video === 'soon') return '<div class="x-video"><img src="img/hall.jpg" alt=""><div class="x-video-over"><strong>The recording of this talk is in preparation</strong><span class="x-video-cap">It appears here as soon as it is on the ERN YouTube channel.</span></div></div>';
    if (a.video !== 'yes') return current().indexOf('a-') === 0 ? '<div class="x-card x-norec">' + ic('video') + '<div><strong>No recording is available for this talk</strong><p class="x-small x-muted" style="margin:.125rem 0 0">The abstract, its references and related talks are on this page.</p></div></div>' : '';
    if (S.playing === a.id) {
      return '<div class="x-video"><img src="img/hall.jpg" alt=""><div class="x-video-over"><span class="x-demo-tag">Mockup</span><strong>The recording plays here' + (S.playAt ? ', from ' + mmss(S.playAt) : '') + '</strong><span class="x-video-cap">On the platform the video comes from the ' + ext('https://www.youtube.com/channel/UCy2Jz5v9oUTD7Y7glRbgBZA', 'ERN YouTube channel') + ' and plays here, without an account.</span><button type="button" class="btn btn-outline x-btn-sm" data-act="play" data-arg="">Stop</button></div><div class="x-video-bar"><i></i></div></div>';
    }
    return '<div class="x-video"><img src="img/hall.jpg" alt="A session in the Main Hall at ERNC26"><div class="x-video-over"><button type="button" class="x-play" data-act="play" data-arg="' + a.id + '" aria-label="Play the recording of this talk">' + PLAY + '</button><span class="x-video-cap">Watch the talk</span></div></div>';
  }
  function related(a) {
    var words = {}; toks(a.kw.join(' ')).forEach(function (w) { if (w.length > 3 && w !== 'esports') words[w] = 1; });
    return D.abstracts.filter(function (b) { return b.id !== a.id; }).map(function (b) {
      var n = 0; toks(b.kw.join(' ') + ' ' + b.title).forEach(function (w) { if (words[w]) n++; });
      a.au.forEach(function (name) { if (b.au.indexOf(name) >= 0) n += 2; });
      return { b: b, n: n };
    }).filter(function (r) { return r.n > 0; }).sort(function (x, y) { return y.n - x.n; }).slice(0, 3).map(function (r) { return r.b; });
  }
  function abstractView(id) {
    var a = ABS[id];
    var idx = D.abstracts.indexOf(a), prev = D.abstracts[idx - 1], next = D.abstracts[idx + 1], on = !!S.saved[a.id];
    var cite = a.aus + ' (2026). ' + a.title + (/[?!.]$/.test(a.title) ? ' ' : '. ') + 'In ERNC26 Book of Abstracts (p. ' + a.page + '). Esports Research Network.';
    var refs = a.refs.map(function (r) {
      var f = fold(r).replace(/[^a-z0-9]+/g, ''), best = null;
      D.pubs.forEach(function (p) { if (f.indexOf(p._c) >= 0 && (!best || p._c.length > best._c.length)) best = p; });
      return '<li>' + esc(r) + (best ? ' <button type="button" class="x-tag" data-act="openpub" data-arg="' + best.id + '">In the database ' + ic('arrow') + '</button>' : '') + '</li>';
    }).join('');
    var rel = related(a);
    return liveBar() + '<div class="container-ern content-wrap x-abstract">' + crumbs([['Conferences', '#conferences'], ['ERNC26', '#ernc26'], ['Abstract']]) +
      '<div class="x-cols"><article>' +
      '<p class="x-small x-muted" style="margin-bottom:.5rem">' + esc(sessionLine(a, true)) + (a.fmt === 'panel' ? ' · Panel' : '') + ' · Conference abstract, as printed in the ERNC26 Book of Abstracts</p>' +
      '<h1 tabindex="-1">' + esc(a.title) + '</h1><p class="x-muted x-lead">' + esc(a.by) + '</p><p class="x-small" style="margin:-.25rem 0 1.25rem"><span class="x-muted">More by </span>' + a.au.map(function (n) { return '<button type="button" class="x-link" data-act="dbauthor" data-arg="' + esc(n.split(' ').pop()) + '">' + esc(n) + '</button>'; }).join(', ') + '</p>' + videoBlock(a) +
      '<div class="x-abstract-body">' + a.sec.map(function (s) { return (s.h ? '<h3>' + esc(s.h) + '</h3>' : '') + '<p>' + esc(s.t) + '</p>'; }).join('') + '</div>' +
      trBlock(a) + '<h3 style="font-size:1.0625rem;margin:1.75rem 0 .25rem">Keywords</h3><p class="x-small x-muted" style="margin-bottom:.625rem">A keyword shows all ERNC26 abstracts that carry it.</p><div class="x-chips">' + a.kw.map(function (k) { return chip(k, 'bookkw', k, false); }).join('') + '</div>' +
      (a.refs.length ? '<h3 style="font-size:1.0625rem;margin:1.75rem 0 .625rem">References</h3><ul class="x-refs">' + refs + '</ul>' : '') +
      '<div class="x-row x-between" style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid hsl(var(--border))">' +
      (prev ? '<a href="#' + prev.id + '" class="btn btn-outline x-btn-sm">Previous abstract</a>' : '<span></span>') + (next ? '<a href="#' + next.id + '" class="btn btn-outline x-btn-sm">Next abstract</a>' : '') + '</div>' +
      '</article><aside class="x-stack" style="gap:1rem">' +
      '<div class="x-card"><h3>Cite this abstract</h3><p class="x-cite" id="cite-text">' + esc(cite) + '</p><div class="x-row" style="margin-top:.75rem"><button type="button" class="btn btn-primary x-btn-sm" data-act="copycite">Copy citation</button>' +
      '<button type="button" class="btn btn-outline x-btn-sm" data-act="save" data-arg="' + a.id + '" aria-pressed="' + on + '">' + ic('save') + (on ? 'Saved' : 'Save') + '</button>' + mk('idea') + '</div></div>' +
      '<div class="x-card"><h3>In the book</h3><p class="x-small">ERNC26 Book of Abstracts, page ' + a.page + '</p><p class="x-small x-muted">' + esc(sessionLine(a, true) || 'Pre-recorded talk') + '</p><a href="#ernc26" data-act="conftab" data-arg="book" class="x-small">Back to all abstracts</a></div>' +
      (rel.length ? '<div class="x-card"><h3>Related talks</h3><div style="display:grid;gap:.75rem">' + rel.map(function (b) { return '<a href="#' + b.id + '" class="x-small" style="font-weight:500">' + esc(b.title) + '<span class="x-muted" style="display:block;font-weight:400">' + esc(b.aus) + '</span></a>'; }).join('') + '</div></div>' : '') +
      '</aside></div></div>';
  }
