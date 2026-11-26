
  /* ---------- about us, as on the current site ---------- */
  function personCard(m) { return '<div class="x-member"><span class="x-av">' + esc(initials(m[0])) + '</span><div><strong>' + esc(m[0]) + '</strong><span>' + esc(m[1]) + '</span><em>' + esc(m[2]) + '</em></div></div>'; }
  function about() {
    var A = D.about, M = A.members, max = M.top[0][1], grey = 'color:hsl(var(--muted-foreground));line-height:1.75';
    return liveBar() + pageHero({ title: esc(A.title), text: esc(A.tagline) }) +
      subnav([['goals', 'Goals'], ['members', 'Members'], ['mission', 'Mission Statement'], ['board', 'Board Members'], ['governance', 'Governance & Volunteers'], ['history', 'History']]) +
      '<section id="sec-goals" class="section-spacing bg-background"><div class="container-ern"><h2>Our Goals</h2><p style="color:hsl(var(--muted-foreground));margin-bottom:2rem;max-width:42rem">' + esc(D.home.goalsLead) + '</p>' + goalsList() + '</div></section>' +
      '<section id="sec-members" class="section-spacing bg-secondary-light"><div class="container-ern"><h2>Members</h2>' +
      '<div class="x-stats"><div class="x-stat"><b>' + M.n + '</b><span>members</span></div><div class="x-stat"><b>' + M.countries + '</b><span>countries</span></div><div class="x-stat"><b>' + M.europe + '</b><span>members in ' + M.europeCountries + ' European countries</span></div></div>' +
      '<div class="x-mapgrid"><img src="img/members.svg" alt="World map of the ERN members by country. Most members live in North America, Europe and Australia."><div><h3 style="font-size:1.125rem;margin-bottom:1rem">Largest groups</h3><ul class="x-bars">' +
      M.top.map(function (t) { return '<li><span>' + esc(t[0]) + '</span><i style="width:' + Math.round(t[1] / max * 100) + '%"></i><span>' + t[1] + '</span></li>'; }).join('') + '</ul></div></div>' +
      '<p class="x-small x-muted" style="font-style:italic;margin:1.5rem 0 0">As of: ' + esc(M.asof) + mk() + '</p>' +
      (S.mark ? '<div style="margin-top:1rem">' + demo('<p>The current page shows a map and a date. On the platform the figures and the map come from the member list.</p>') + '</div>' : '') + '</div></section>' +
      '<section id="sec-mission" class="section-spacing bg-background"><div class="container-ern"><div class="about-section-with-image"><div><h2>Mission Statement</h2><div style="' + grey + '">' + paras(A.mission) + '</div></div>' +
      '<div><img src="img/hall.jpg" alt="A session in the Main Hall at ERNC26" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></div></div></div></section>' +
      '<section id="sec-board" class="section-spacing bg-secondary-light"><div class="container-ern"><h2>Board Members</h2><div class="x-team">' + A.board.map(personCard).join('') + '</div></div></section>' +
      '<section id="sec-governance" class="section-spacing bg-background"><div class="container-ern"><h2>Governance &amp; Volunteers</h2><div class="x-team">' + A.governance.map(personCard).join('') + '</div></div></section>' +
      '<section id="sec-history" class="section-spacing bg-secondary-light"><div class="container-ern"><div style="max-width:48rem"><h2>Our History</h2><div style="' + grey + '">' + paras(A.history) + '</div>' +
      '<h3 style="font-size:1.25rem;margin:2.5rem 0 .75rem">Former ERN Board Members</h3><p style="' + grey + ';margin:0">' + esc(A.former) + '</p></div></div></section>';
  }

  /* ---------- get involved, as on the current site ---------- */
  function involved() {
    var I = D.involved, grey = 'color:hsl(var(--muted-foreground));line-height:1.75';
    return liveBar() + pageHero({ title: 'Get Involved', text: esc(I.lead) }) +
      subnav([['join', 'Join'], ['community', 'Community'], ['ways', 'Ways to Participate'], ['start', 'How to Get Started']]) +
      '<section id="sec-join" class="section-spacing bg-background"><div class="container-ern"><div class="section-header"><h2>Get Involved</h2><p>Multiple ways to participate in the ERN community</p></div><div class="quick-actions-grid">' + involvedCards() + '</div></div></section>' +
      '<section id="sec-community" class="section-spacing bg-secondary-light"><div class="container-ern"><div class="about-section-with-image"><div><h2>A Community-Led Network</h2><div style="' + grey + '">' + paras(I.community) + '</div></div>' +
      '<div><img src="img/group.jpg" alt="The participants of ERNC26 in Tsukuba, September 2026" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></div></div></div></section>' +
      '<section id="sec-ways" class="section-spacing bg-background"><div class="container-ern"><div class="section-header"><h2>Ways to Participate</h2><p>Participation can be formal or informal, short-term or ongoing. Every contribution matters.</p></div><div class="quick-actions-grid">' +
      I.ways.map(function (w) { return '<div class="action-card"><div class="action-icon">' + ic(w[0]) + '</div><h3>' + esc(w[1]) + '</h3><p>' + esc(w[2]) + '</p></div>'; }).join('') + '</div></div></section>' +
      '<section id="sec-start" class="section-spacing bg-secondary-light"><div class="container-ern"><div style="max-width:48rem"><h2>How to Get Started</h2><p style="' + grey + '">' + esc(I.start) + '</p>' +
      '<div class="hero-buttons" style="animation:none;margin-top:1.5rem"><a href="#join" class="btn btn-primary btn-lg">Become a Member</a>' + ext(I.discord, 'Join Discord', 'btn btn-outline btn-lg') + '</div>' +
      '<p class="x-small x-muted" style="margin:1.25rem 0 0">Contact the board: board@esportsresearch.net</p></div></div></section>';
  }

  /* ---------- forms ---------- */
  function fld(id, label, o) {
    o = o || {};
    var common = ' class="x-input" id="' + id + '" name="' + id + '"' + (o.req ? ' aria-required="true"' : '') + (o.di ? ' data-input="' + o.di + '"' : '') + ' aria-describedby="' + id + '-err"', input;
    if (o.opts) input = '<select' + common + '><option value="">Please choose</option>' + o.opts.map(function (v) { return '<option>' + esc(v) + '</option>'; }).join('') + '</select>';
    else if (o.area) input = '<textarea' + common + ' rows="' + (o.rows || 4) + '"></textarea>';
    else input = '<input' + common + ' type="' + (o.type || 'text') + '"' + (o.ph ? ' placeholder="' + esc(o.ph) + '"' : '') + ' autocomplete="off">';
    return '<label class="x-field" for="' + id + '"><span>' + esc(label) + (o.req ? ' <span class="x-req" aria-hidden="true">*</span>' : (o.plain ? '' : ' <small>(optional)</small>')) + '</span>' + input +
      (o.help ? '<small>' + esc(o.help) + '</small>' : '') + '<span class="x-err" id="' + id + '-err" hidden></span></label>';
  }
  function validate(form, rules) {
    var first = null;
    rules.forEach(function (r) {
      var el = form.querySelector('#' + r[0]), err = form.querySelector('#' + r[0] + '-err');
      var v = el.type === 'checkbox' ? el.checked : el.value.trim();
      var bad = r[2] ? !r[2](v) : !v;
      el.setAttribute('aria-invalid', bad ? 'true' : 'false');
      if (err) { err.textContent = bad ? r[1] : ''; err.hidden = !bad; }
      if (bad && !first) first = el;
    });
    if (first) { first.focus(); toast('Please check the marked fields.'); }
    return !first;
  }
  function isMail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

  /* ---------- become a member: the online application ---------- */
  function join() {
    var head = pageHero({ crumbs: [['Get Involved', '#involved'], ['Become a member']], title: 'Become a member' + mk(), text: 'Apply online. The board confirms every application.' });
    var steps = '<div class="x-card"><h3>What happens next</h3><ol class="x-steps"><li>The board reviews your application.</li><li>You receive a welcome e-mail with your sign-in for the member area.</li><li>From then on you keep your own details up to date in your profile.</li></ol></div>';
    var facts = '<div class="x-card"><h3>Before you apply</h3><dl class="x-facts x-small"><div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>Cost</dt><dd>Membership is free.</dd></div><div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>Who can join</dt><dd>Researchers, students and practitioners with an interest in esports research.</dd></div><div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>Approval</dt><dd>The board confirms every application. You hear from us by e-mail.</dd></div></dl></div>';
    if (S.joined) {
      return liveBar() + head + '<div class="container-ern content-wrap"><div class="x-cols"><div class="x-stack"><div class="x-card"><div class="x-success">' + ic('check') + '<div><h3>Thank you, ' + esc(S.joined) + '. Your application is received</h3><p style="margin:0">We sent a copy to your e-mail address.</p></div></div>' +
        '<ol class="x-timeline" style="margin-top:1.25rem"><li><strong>Application received</strong><span>Today</span></li><li class="x-wait"><strong>Waiting for the board</strong><span>The board confirms every application. You do not need to do anything.</span></li><li class="x-todo"><strong>Welcome e-mail</strong><span>It holds the link to set your password for the member area.</span></li></ol></div>' +
        '<div class="x-card"><h3>No e-mail from us?</h3><p class="x-small" style="margin:0">Look in the spam folder first. If nothing is there, write to <strong>board@esportsresearch.net</strong> and name the address you applied with.</p></div>' +
        demo('<p>Nothing was sent. This is how the confirmation looks.</p><div class="x-row"><a href="#members" data-act="activate">Skip ahead: open the link in the welcome e-mail</a><button type="button" class="x-link" data-act="joinreset">Show the form again</button></div>') +
        '<div><a href="#involved" class="btn btn-outline x-btn-sm">Other ways to get involved</a></div></div><aside>' + steps + '</aside></div></div>';
    }
    return liveBar() + head + '<div class="container-ern content-wrap"><div class="x-cols"><div class="x-stack">' +
      demo('<p><strong>Nothing you enter here is sent.</strong> To join ERN today, use the form on the current website.</p>' + ext('https://esportsresearch.net/getinvolved/', 'Open the current page')) +
      '<form class="x-card x-form" data-form="join" novalidate><p class="x-small x-muted" style="margin:0">Fields marked with <span class="x-req">*</span> are required.</p>' +
      '<div class="x-two">' + fld('join-first', 'First name', { req: 1 }) + fld('join-last', 'Last name', { req: 1 }) + '</div>' +
      fld('join-mail', 'E-mail address', { req: 1, type: 'email', ph: 'you@university.edu' }) +
      fld('join-inst', 'University or organisation', { req: 1 }) +
      '<div class="x-two">' + fld('join-country', 'Country', { req: 1 }) + fld('join-city', 'City') + '</div>' +
      fld('join-status', 'I am a', { req: 1, opts: ['PhD student', 'Researcher with a PhD', 'Professor', 'Student', 'Practitioner in industry', 'Other'] }) +
      fld('join-interests', 'Research interests', { area: 1, rows: 3, help: 'A few keywords are enough. Other members find you by them.' }) +
      fld('join-discord', 'Discord name', { help: 'Optional. With it you get the member role on the ERN Discord.' }) +
      '<div><label class="x-check" for="join-agree"><input type="checkbox" id="join-agree" name="join-agree" aria-describedby="join-agree-err"><span>I have read the <a href="#conduct">Code of Conduct</a> and the <a href="#imprint">privacy policy</a>. <span class="x-req" aria-hidden="true">*</span></span></label><span class="x-err" id="join-agree-err" hidden></span></div>' +
      '<div><button class="btn btn-primary" type="submit">Send application</button></div></form></div><aside class="x-stack" style="gap:1rem">' + facts + steps + '</aside></div></div>';
  }

  /* ---------- code of conduct, as on the current site ---------- */
  function conduct() {
    var C = D.conduct;
    return liveBar() + pageHero({ title: 'Code of Conduct', text: esc(C.lead) }) + subnav(C.nav) +
      '<div class="container-ern content-wrap"><div class="x-cols x-side-first"><article class="entry-content x-prose"><p class="x-small x-muted" style="margin-bottom:2rem">' + esc(C.version) + '</p>' + C.html + '</article>' +
      '<aside class="x-card"><h3>Something happened?</h3><p class="x-small x-muted">You can report a concern at any time, also anonymously. The ERN Ombudsperson reads every report.</p><a href="#concern" class="btn btn-primary x-btn-sm">Report a concern</a></aside></div></div>';
  }

  /* ---------- report a concern, as on the current site ---------- */
  function concern() {
    var C = D.concern;
    var head = pageHero({ title: esc(C.title), text: 'Anonymous, and read by the ERN Ombudsperson.' });
    if (S.concernSent) {
      return liveBar() + head + '<div class="container-ern content-wrap"><div class="x-stack" style="max-width:48rem"><div class="x-card"><div class="x-success">' + ic('check') + '<div><h3>Thank You</h3><p>' + esc(C.done) + '</p></div></div></div>' +
        demo('<p><strong>Nothing was sent.</strong> This is how the confirmation looks. To report a real concern, use the form on the current website.</p>' + ext('https://esportsresearch.net/concern/', 'Open the real form') + '<button type="button" class="x-link" data-act="concernreset">Show the form again</button>') + '</div></div>';
    }
    return liveBar() + head + '<div class="container-ern content-wrap"><div class="x-cols x-side-first"><div class="x-stack">' +
      demo('<p><strong>Nothing you enter here is sent.</strong> To report a real concern, use the form on the current website.</p>' + ext('https://esportsresearch.net/concern/', 'Open the real form')) +
      '<div class="x-prose" style="font-size:1rem">' + paras(C.intro) + '</div>' +
      '<form class="x-card x-form" data-form="concern" novalidate><p class="x-small x-muted" style="margin:0">Fields marked with <span class="x-req">*</span> are required.</p>' +
      fld('con-event', 'Please select which ERN event or platform your concern relates to', { req: 1, opts: C.events }) +
      fld('con-event-other', 'If you selected other, which ERN event?', { plain: 1 }) +
      fld('con-type', 'What type of concern would you like to share with us?', { req: 1, opts: C.types }) +
      fld('con-type-other', 'If you selected other, please elaborate', { plain: 1 }) +
      fld('con-text', 'Could you please provide a detailed description of your concern or experience?', { req: 1, area: 1, rows: 6, help: C.help1 }) +
      fld('con-more', 'Is there anything else you would like us to know or take into account?', { area: 1, plain: 1, help: C.help2 }) +
      '<fieldset style="border:0;padding:0;margin:0;min-width:0"><legend class="x-small" style="font-weight:500;padding:0;margin-bottom:.375rem;color:hsl(var(--foreground) / .85)">Name (not required)</legend><div class="x-two">' +
      '<label class="x-field" for="con-first"><input class="x-input" id="con-first" type="text" autocomplete="off"><small>First</small></label><label class="x-field" for="con-last"><input class="x-input" id="con-last" type="text" autocomplete="off"><small>Last</small></label></div></fieldset>' +
      fld('con-mail', 'Email (not required)', { type: 'email', plain: 1 }) +
      '<div><button class="btn btn-primary" type="submit">Submit</button></div></form></div>' +
      '<aside class="x-card"><h3>Good to know</h3><ul class="x-plain"><li>You do not have to give your name or e-mail address.</li><li>Reports may be made during or after an event.</li><li>The rules that apply are in the <a href="#conduct" class="x-u">Code of Conduct</a>.</li></ul></aside></div></div>';
  }

  /* ---------- imprint ---------- */
  function imprint() {
    var I = D.imprint;
    return liveBar() + pageHero({ title: 'Imprint / Privacy Policy' }) +
      '<div class="container-ern content-wrap"><div class="x-cols"><article class="entry-content x-prose"><h2>Imprint</h2><p>' + I.lines.map(esc).join('<br>') + '</p><p>Legal Representative: ' + esc(I.rep) + '</p>' +
      '<h2>Privacy Policy</h2><p>The privacy policy of the current website stays in force. It has these sections:</p><ol>' + I.policy.map(function (p) { return '<li>' + esc(p) + '</li>'; }).join('') + '</ol>' +
      '<p>' + ext('https://esportsresearch.net/imprint/', 'Read the full text on the current site') + '</p></article>' +
      '<aside>' + demo('<p>The platform adds accounts, a link to Discord and an assistant. The privacy policy has to cover them before the launch.</p>') + '</aside></div></div>';
  }
