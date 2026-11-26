
  /* ---------- member area ---------- */
  var NOTBUILT = 'Not built in this mockup: ';
  function dcName(p) { return p.name === S.me.name ? (S.me.dcshow ? S.me.discord : '') : fold(p.name).replace(/[^a-z]+/g, '.'); }
  function dcLine(p) {
    var n = dcName(p); if (!n) return '';
    return '<p class="x-small x-dc"><span class="x-muted">Discord' + mk() + '</span><button type="button" class="x-link" data-act="copydc" data-arg="' + esc(n) + '" aria-label="Copy the Discord name ' + esc(n) + '">' + esc(n) + '</button></p>';
  }
  function dcCard() {
    return '<div class="x-card"><h3>ERN on Discord' + mk('idea') + '</h3><p class="x-small x-muted">The network talks on Discord. An ERN bot can connect it with the platform. It can:</p><ul class="x-small x-botlist">' +
      ['Give members the member role, once the Discord name is in the profile', 'Take a report for the ombudsperson with /report, as the Code of Conduct describes', 'Post new journal articles once a week, with news, deadlines and reminders for lectures',
       'Find members by topic or country', 'Search the database from Discord', 'Pair two members for a monthly coffee chat'].map(function (t) { return '<li>' + t + '</li>'; }).join('') + '</ul>' +
      ext(D.involved.discord, 'Open the ERN Discord', 'x-small') + '</div>';
  }
  function rowLink(href, attrs, meta, title, by) {
    return '<li class="x-item"><div class="x-item-row"><a class="x-item-main" href="' + href + '"' + (attrs || '') + '><span class="x-item-meta">' + meta + '</span><span class="x-item-title">' + title + '</span><span class="x-item-by">' + by + '</span></a><span class="x-item-side">' + ic('arrow') + '</span></div></li>';
  }
  function overview() {
    var m = S.me, sub = S.subs[S.subs.length - 1], ga = D.notices[0], el = D.notices[1];
    var things = [['save', savedTotal(), 'Items in my saved library', '#saved', ''], ['cal', agendaCount(), 'Talks in my agenda', '#ernc26', ' data-act="conftab" data-arg="prog"'], ['doc', S.subs.length, 'ERNC27 submissions', '#ernc27', ' data-act="c27tab" data-arg="submit"']];
    return '<div class="x-cols x-wide-side"><div class="x-stack" style="gap:2rem"><div><h2 class="x-h2">For you</h2><ul class="x-list">' +
      (sub ? rowLink('#submission', '', 'Your ERNC27 submission · ' + sub.no, esc(sub.title), S.adm.dec[sub.no] && S.adm.dec[sub.no].sent ? '<strong>Decision: ' + esc(S.adm.dec[sub.no].res) + '.</strong> Open your submission page for the feedback.' : 'In review. Decisions go out on 15 March 2027.')
           : rowLink('#ernc27', ' data-act="c27tab" data-arg="submit"', 'ERNC27 in Raleigh', 'You have not submitted an abstract yet', 'Abstracts are due on Monday 4 January 2027.')) +
      (S.sub.savedAt && S.sub.stage !== 'done' ? rowLink('#ernc27', ' data-act="subresume"', 'Your ERNC27 draft', esc(S.sub.vals['sub-title'] || 'Untitled draft'), 'Last saved at ' + S.sub.savedAt + '. Abstracts are due on 4 January 2027.') : '') +
      rowLink('#members', ' data-act="memtab" data-arg="notices"', esc(ga.k), esc(ga.t), esc(ga.x)) +
      rowLink('#members', ' data-act="memtab" data-arg="notices"', esc(el.k), esc(el.t), esc(el.x)) +
      rowLink('#lec-1', '', 'Autumn lecture series', 'Next lecture: ' + esc(progOf(0).title), COMING[0][0] + ', 16:00 (Oslo time)') + (S.props.length ? rowLink('#mylectures', '', 'My lectures', esc(S.props[S.props.length - 1].title), esc(S.adm.out[S.props[S.props.length - 1].id] || WF[stepOf(S.props[S.props.length - 1])])) : '') + '</ul></div>' +
      '<div><h2 class="x-h2">Your things</h2><div class="x-things">' + things.map(function (t) {
        return '<a class="x-card x-thing" href="' + t[3] + '"' + t[4] + '>' + ic(t[0]) + '<b>' + t[1] + '</b><span>' + t[2] + '</span></a>';
      }).join('') + '</div></div>' +
      '<div><div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">From the ERN</h2><a class="x-small" href="#news">All news</a></div><ul class="x-list">' + D.news.slice(0, 3).map(function (n) {
        return '<li class="x-item"><div class="x-item-row"><a class="x-item-main" ' + extAttr(n.url) + '><span class="x-item-meta">' + esc(n.d) + '</span><span class="x-item-title">' + esc(n.t) + '</span><span class="x-vh"> (opens in a new tab)</span></a><span class="x-item-side">' + ic('ext') + '</span></div></li>';
      }).join('') + '</ul></div></div>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>Your membership</h3><dl class="x-facts x-small" style="margin-bottom:1rem">' +
      [['Member since', 'March 2025'], ['Status', esc(m.status)], ['Listed as', esc(m.inst) + ', ' + esc(m.country)], ['Directory', m.listed ? 'Visible to members' : 'Hidden'], ['Discord name', m.discord ? esc(m.discord) : 'Not given']].map(function (d) { return '<div style="grid-template-columns:7rem minmax(0,1fr)"><dt>' + d[0] + '</dt><dd>' + d[1] + '</dd></div>'; }).join('') +
      '</dl><button type="button" class="btn btn-outline x-btn-sm" data-act="memtab" data-arg="profile">Update my details</button></div>' + dcCard() +
      demo('<p>The button “Admin area” at the top is shown to board members and admins only. Here it is open to try.</p>') + '</aside></div>';
  }
  function slug(n) { return fold(n).replace(/[^a-z]+/g, '-'); }
  function dirSel(id, label, key, cur) {
    var seen = {}; D.people.forEach(function (p) { [].concat(p[key]).forEach(function (v) { seen[v] = 1; }); });
    return sel(id, label, [['', 'All']].concat(Object.keys(seen).sort().map(function (v) { return [v, v]; })), cur);
  }
  function researcher(p) {
    if (!S.user) return liveBar() + pageHero({ title: 'Member directory', text: 'Researcher pages are for ERN members.' }) + '<div class="container-ern content-wrap"><a href="#members" class="btn btn-primary">Sign in</a></div>';
    return liveBar() + '<div class="container-ern content-wrap x-abstract">' + crumbs([['Member area', '#members'], ['Directory', '#members'], [p.name]]) +
      '<div class="x-cols"><article class="x-stack" style="gap:1.5rem"><div class="x-row"><span class="x-av" style="width:3.5rem;height:3.5rem;font-size:1.125rem">' + esc(p.ini) + '</span><div><h1 tabindex="-1" style="margin:0">' + esc(p.name) + '</h1><span class="x-muted">' + esc(p.role) + ' · ' + esc(p.country) + '</span></div></div>' +
      '<div><h3 style="font-size:1.0625rem;margin-bottom:.5rem">Research interests</h3><div class="x-item-tags">' + p.topics.map(function (t) { return tag(esc(t), 1); }).join('') + '</div></div>' +
      '<div><h3 style="font-size:1.0625rem;margin-bottom:.5rem">Open to</h3><p style="margin:0">' + esc(p.open) + '</p></div>' +
      '<div><h3 style="font-size:1.0625rem;margin-bottom:.5rem">Publications, abstracts and talks</h3><div class="x-empty" style="padding:1.25rem">On the platform this list fills itself from the database and the conference abstracts, by the name and the ORCID iD of the member.</div></div>' +
      demo('<p>' + esc(p.name) + ' is an invented example, so there is no work to list.</p>') + '</article>' +
      '<aside class="x-stack" style="gap:1rem"><form class="x-card" data-form="rmsg"><h3>Write to ' + esc(p.name.split(' ')[0]) + '</h3><label class="x-field" for="rmsg-in" style="margin-bottom:.75rem"><span class="x-vh">Your message</span><textarea class="x-input" id="rmsg-in" rows="4" placeholder="Introduce yourself and say what you have in mind."></textarea></label><button class="btn btn-primary x-btn-sm" type="submit">Send through ERN</button>' +
      '<p class="x-small x-muted" style="margin:.75rem 0 0">ERN forwards your message by e-mail. Your address is shown, theirs stays private until they answer.</p></form>' +
      '<div class="x-card"><h3>On Discord</h3>' + dcLine(p).replace('class="x-small x-dc"', 'class="x-small x-dc" style="margin:0"') + '</div></aside></div></div>';
  }
  function peopleList() {
    var ts = toks(S.memQ);
    var list = D.people.filter(function (p) { var h = fold(p.name + ' ' + p.role + ' ' + p.country + ' ' + p.topics.join(' ') + ' ' + p.open); return ts.every(function (t) { return h.indexOf(t) >= 0; }) && (!S.dirTopic || p.topics.indexOf(S.dirTopic) >= 0) && (!S.dirCountry || p.country === S.dirCountry) && (!S.dirRole || p.role === S.dirRole) && (!S.dirOpen || p.open === S.dirOpen); });
    var on = ts.length || S.dirTopic || S.dirCountry || S.dirRole || S.dirOpen;
    return '<p class="x-filterline" aria-live="polite"><span>' + (on ? list.length + ' of ' + D.people.length + ' example members' : D.people.length + ' example members') + '</span>' + (on ? '<button type="button" class="x-link" data-act="dirclear">Clear search and filters</button>' : '') + '</p>' +
      (list.length ? '<div class="x-people">' + list.map(function (p) {
        return '<div class="x-card x-person"><span class="x-av">' + esc(p.ini) + '</span><div><a class="x-pname" href="#r-' + slug(p.name) + '">' + esc(p.name) + '</a><span class="x-small x-muted">' + esc(p.role) + ' · ' + esc(p.country) + '</span><div class="x-item-tags">' + p.topics.map(function (t) { return tag(esc(t), 1); }).join('') + '</div><p class="x-small" style="margin:.625rem 0 0">Open to: ' + esc(p.open) + '</p>' + dcLine(p) + '</div></div>';
      }).join('') + '</div>' : '<div class="x-empty">No member found. Try a country or a topic, for example Japan or governance.</div>');
  }
  function memBody() {
    var m = S.me;
    if (S.memTab === 'overview') return overview();
    if (S.memTab === 'articles') {
      return '<p class="x-small x-muted x-lead" style="margin:0">' + NEWPUBS.length + ' articles since 1 October 2026, picked up automatically from Crossref, OpenAlex and PubMed. A click opens the entry in the database.</p><ul class="x-list">' +
        NEWPUBS.map(function (p) {
          return '<li class="x-item"><div class="x-item-row"><a class="x-item-main" href="#database" data-act="openpub" data-arg="' + p.id + '"><span class="x-item-meta">' + esc(p.v.split(',')[0]) + ' · added ' + fmtDate(p.added) + '</span><span class="x-item-title">' + esc(p.t) + '</span><span class="x-item-by">' + esc(p.au) + '</span>' +
            ((p.oa || p.from) ? '<span class="x-item-tags">' + (p.oa ? tag('Open access', 1) : '') + (p.from ? tag('First presented at ERNC26') : '') + '</span>' : '') + '</a><span class="x-item-side">' + ic('arrow') + '</span></div></li>';
        }).join('') + '</ul>';
    }
    if (S.memTab === 'press') {
      var srcs = ['all', 'The Esports Radar', 'The Esports Advocate'];
      var items = D.press.filter(function (n) { return S.pressSrc === 'all' || n.src === S.pressSrc; });
      return '<p class="x-small x-muted x-lead" style="margin:0">Collected automatically from the sources below, last on 10 November 2026. Each headline opens the original article at its source.</p>' +
        '<div class="x-chips" role="group" aria-label="Source">' + srcs.map(function (x) { return chip(x === 'all' ? 'All sources' : x, 'presssrc', x, S.pressSrc === x); }).join('') + '</div>' +
        '<ul class="x-list">' + items.map(function (n) {
          return '<li class="x-item"><div class="x-item-row"><a class="x-item-main" ' + extAttr(n.url) + '><span class="x-item-meta">' + esc(n.src) + ' · ' + esc(n.when) + '</span><span class="x-item-title">' + esc(n.t) + '</span><span class="x-item-tags">' + tag(esc(n.tag), 1) + '</span><span class="x-vh"> (opens in a new tab)</span></a><span class="x-item-side">' + ic('ext') + '</span></div></li>';
        }).join('') + '</ul>';
    }
    if (S.memTab === 'people') {
      return '<div class="x-search">' + ic('search') + '<label class="x-vh" for="mem-q">Search the member directory</label><input id="mem-q" type="search" data-input="memq" value="' + esc(S.memQ) + '" placeholder="Search by name, country or research topic" autocomplete="off"></div>' +
        '<div class="x-filters">' + dirSel('dir-topic', 'Topic', 'topics', S.dirTopic) + dirSel('dir-country', 'Country', 'country', S.dirCountry) + dirSel('dir-role', 'Career stage', 'role', S.dirRole) + dirSel('dir-open', 'Open to', 'open', S.dirOpen) + mk() + '</div><p class="x-small x-muted" style="margin:0">Open a member to write through ERN by e-mail. Discord is optional.</p>' +
        demo('<p>ERN has ' + D.about.members.n + ' members in ' + D.about.members.countries + ' countries. The people shown here are invented examples.</p>') + '<div id="mem-results" class="x-stack" style="gap:.875rem">' + peopleList() + '</div>';
    }
    if (S.memTab === 'notices') {
      return '<div style="display:grid;gap:1rem">' + D.notices.map(function (n) {
        return '<div class="x-card">' + tag(esc(n.k)) + '<h3 style="margin-top:.75rem">' + esc(n.t) + '</h3><p>' + esc(n.x) + '</p><div class="x-row">' + n.docs.map(function (d) { return '<button type="button" class="badge-circle" data-act="toast" data-arg="' + NOTBUILT + 'the document would open here, for members only.">' + ic('doc', 'x-ic-sm') + '&nbsp;' + esc(d) + '</button>'; }).join('') + '</div></div>';
      }).join('') + '</div>';
    }
    if (S.memTab === 'profile') {
      var field = function (id, label, val) { return '<label class="x-field" for="pf-' + id + '"><span>' + label + '</span><input class="x-input" id="pf-' + id + '" name="' + id + '" type="text" value="' + esc(val) + '" autocomplete="off"></label>'; };
      var check = function (id, label, on) { return '<label class="x-check" for="pf-' + id + '"><input type="checkbox" id="pf-' + id + '" name="' + id + '"' + (on ? ' checked' : '') + '><span>' + label + '</span></label>'; };
      return '<form class="x-card x-profile" data-form="profile" style="max-width:40rem"><h3>My details</h3><p class="x-small x-muted">Keep your entry in the member list up to date yourself. No new application is needed.</p>' +
        field('name', 'Name', m.name) + field('inst', 'University or organisation', m.inst) + field('country', 'Country', m.country) + field('status', 'Status', m.status) + field('interests', 'Research interests', m.interests) + field('orcid', 'ORCID iD', m.orcid) + field('discord', 'Discord name' + mk(), m.discord) +
        '<h3 style="margin-top:1.5rem">Visibility</h3>' + check('listed', 'Show me in the member directory', m.listed) + check('collab', 'I am open to collaboration requests', m.collab) + check('dcshow', 'Show my Discord name in the member directory', m.dcshow) +
        '<h3 style="margin-top:1.5rem">I am available as' + mk('idea') + '</h3>' + check('reviewer', 'Reviewer for the ERN Conference', m.reviewer) + check('mentor', 'Mentor for early-career members', m.mentor) + check('media', 'Contact for media and industry enquiries', m.media) + check('present', 'I am interested in presenting a lecture', m.present) +
        '<button class="btn btn-primary" type="submit" style="margin-top:.75rem">Save changes</button></form>';
    }
    return overview();
  }
  function members() {
    if (!S.user) {
      return liveBar() + pageHero({ title: 'Member area' + mk(), text: 'For ERN members only: your own overview, new journal articles, the esports press, the member directory and notices.' }) +
        '<div class="container-ern content-wrap"><div class="x-cols x-wide-side"><div class="x-stack" style="max-width:28rem">' + (S.loginMode === 'activate' ? '<form class="x-card x-login" data-form="activate" novalidate><h3>Welcome to ERN</h3><p class="x-small x-muted">The board has confirmed your membership. Choose a password to open the member area.</p><label class="x-field" for="act-pw"><span>New password</span><input class="x-input" id="act-pw" type="password" autocomplete="off"><small>At least eight characters.</small></label><label class="x-field" for="act-pw2"><span>Repeat the password</span><input class="x-input" id="act-pw2" type="password" autocomplete="off"></label><div class="x-row x-between"><button class="btn btn-primary" type="submit">Set password and sign in</button><button type="button" class="x-link" data-act="loginmode" data-arg="">I already have a password</button></div></form>'
        : S.loginMode === 'reset' ? '<form class="x-card x-login" data-form="pwreset" novalidate><h3>Set a new password</h3><p class="x-small x-muted">Enter the e-mail address of your membership. We send you a link.</p><label class="x-field" for="reset-mail"><span>E-mail address</span><input class="x-input" id="reset-mail" type="email" autocomplete="off" placeholder="you@university.edu"></label><div class="x-row"><button class="btn btn-primary" type="submit">Send the link</button><button type="button" class="x-link" data-act="loginmode" data-arg="">Back to sign in</button></div></form>'
        : S.loginMode === 'sent' ? '<div class="x-card"><div class="x-success">' + ic('check') + '<div><h3>Check your e-mail</h3><p class="x-small">If this address belongs to a member, a link to set a new password is on its way.</p><p class="x-small x-muted" style="margin:0">Nothing after ten minutes? Look in the spam folder, then write to board@esportsresearch.net.</p></div></div><button type="button" class="x-link" data-act="loginmode" data-arg="" style="margin-top:.75rem">Back to sign in</button></div>'
        : '<form class="x-card x-login" data-form="login" novalidate><h3>Sign in</h3>' +
        '<label class="x-field" for="login-mail"><span>E-mail address</span><input class="x-input" id="login-mail" type="email" autocomplete="off" placeholder="you@university.edu"></label>' +
        '<label class="x-field" for="login-pw"><span>Password</span><input class="x-input" id="login-pw" type="password" autocomplete="off"></label>' +
        '<div class="x-row x-between"><button class="btn btn-primary" type="submit">Sign in</button><button type="button" class="x-link" data-act="loginmode" data-arg="reset">Forgot your password?</button></div>' +
        '<p class="x-small x-muted" style="margin:1rem 0 0">New member? Your welcome e-mail holds a link to set your password first.</p></form>') +
        demo('<p>No account is needed here.</p><button type="button" class="btn btn-outline x-btn-sm" data-act="login">Enter as a demo member</button>') +
        '<p class="x-small x-muted" style="margin:0">Not a member yet? <a href="#join">Apply online</a>.</p></div>' +
        '<div class="x-card"><h3>What members find here</h3><div class="goals-list" style="grid-template-columns:1fr">' +
        [['compass', 'Your overview', 'Your submissions, your saved talks and what is coming up in the ERN.'], ['doc', 'New journal articles', 'What is new in esports research, picked up as it is published.'], ['globe', 'Esports press', 'The headlines of the industry press, collected every day.'],
         ['users', 'Member directory', 'Find members by country and research topic.'], ['cal', 'Notices', 'General Assembly, elections and documents for members.'], ['pen', 'Your profile', 'Update your own details at any time, with your Discord name.']].map(function (g) {
          return '<div class="goal-item"><div class="goal-item-content"><div class="goal-icon">' + ic(g[0]) + '</div><div><h3>' + g[1] + '</h3><p>' + g[2] + '</p></div></div></div>';
        }).join('') + '</div></div></div></div>';
    }
    var tabs = [['overview', 'Overview'], ['articles', 'New articles', NEWPUBS.length, mk()], ['press', 'Press', D.press.length, mk()], ['people', 'Directory'], ['notices', 'Notices'], ['profile', 'My profile']];
    return liveBar() + '<div class="container-ern content-wrap x-stack">' +
      '<div class="x-row x-between"><div class="x-row"><span class="x-av x-org">' + esc(S.me.ini) + '</span><div><h1 tabindex="-1" style="font-size:1.75rem">Welcome back, ' + esc(S.me.name.split(' ')[0]) + '</h1><span class="x-small x-muted">' + esc(S.me.status) + ' · ' + esc(S.me.inst) + '</span></div></div>' +
      '<div class="x-row">' + lockTag() + '<a href="#admin" data-act="adminin" class="btn btn-outline x-btn-sm">Admin area' + mk() + '</a><button type="button" class="btn btn-outline x-btn-sm" data-act="logout">Sign out</button></div></div>' +
      tabsRow('memtab', tabs, S.memTab, 'Member area').replace('class="x-tabs"', 'class="x-tabs x-many"') +
      '<div id="mem-body" class="x-stack" style="gap:1rem" role="tabpanel">' + memBody() + '</div></div>';
  }
