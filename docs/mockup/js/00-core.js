  var D = JSON.parse(document.getElementById('ern-data').textContent);
  var view = document.getElementById('view');
  var S = {
    mark: false, live: false, user: null, playing: '', pendingScroll: '',
    dbQ: '', dbType: 'all', dbNew: false, dbList: '', dbView: 'search', dbFilters: false, dbField: '', dbYears: '', dbSort: 'new', dbOA: false, dbAuthor: '', dbOpen: '', dbBib: '', dbLimit: 20, myList: {}, trQ: '', playAt: 0, watched: {},
    bookQ: '', bookDay: 'all', bookVideo: false, bookSaved: false, bookKw: '', confTab: 'book', pastTab: {}, saved: {},
    pressSrc: 'all',
    memTab: 'overview', memQ: '', posts: D.posts.slice(), liked: {},
    chat: D.chat.slice(), askLog: [], joined: '', concernSent: false,
    agenda: {}, searches: [], dbLang: '', loginMode: '', synced: false, lecTab: 'watch', tuTopic: '', tuLevel: '', tuReady: false, planLen: '60', matOpen: false, lti: false, attended: {}, proposed: '', props: [], admLec: 'proposals', notes: {}, c27Tab: 'call', subs: [], subDone: false, sub: { vals: {}, stage: 'form', savedAt: '', offline: false, error: '', revising: '' }, subView: 'review', evType: 'all', dirTopic: '', dirCountry: '', dirRole: '', dirOpen: '', progDay: 'Tue', progMine: false, admin: false, admTab: 'overview', pkgOpen: -1, lecStage: 'upcoming', report: '', ws: { up: {}, sent: {} },
    adm: { out: {}, fb: {}, prog: { 0: 'ex2' }, slot: { ex2: 0 }, unit: {}, unitOpen: '', unitPrev: false, progPrev: false, wf: {}, lecOpen: '', hist: {}, apps: {}, queue: {}, status: {}, dec: {}, open: '', roles: {}, edit: '', assign: {}, confs: [], add: '', extra: {} },
    me: Object.assign({ listed: true, collab: true, reviewer: false, mentor: false, media: false, dcshow: true, dcnews: false }, D.me)
  };

  /* ---------- helpers ---------- */
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function fold(s) { return String(s || '').toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, ''); }
  function toks(q) { return fold(q).split(/[^a-z0-9]+/).filter(Boolean); }
  function hl(text, q) {
    var out = esc(text);
    toks(q).forEach(function (t) {
      if (t.length < 3 || /^(amp|quot|lt|gt)$/.test(t)) return;
      out = out.replace(new RegExp('(' + t + ')', 'gi'), '<mark>$1</mark>');
    });
    return out;
  }
  var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  function fmtDate(iso) { var p = iso.split('-'); return Number(p[2]) + ' ' + MONTHS[Number(p[1]) - 1] + ' ' + p[0]; }
  var P = {
    search: 'M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z',
    cal: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    clock: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    globe: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    book: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    video: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    live: 'M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M8.464 15.536a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M13 12a1 1 0 11-2 0 1 1 0 012 0z',
    chat: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    arrow: 'M9 5l7 7-7 7',
    down: 'M19 9l-7 7-7-7',
    ext: 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14',
    heart: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    doc: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    send: 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
    lock: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    refresh: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
    save: 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z',
    check: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    mic: 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z',
    cap: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
    bulb: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    pen: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    award: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    hand: 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11',
    mega: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
    compass: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
    coffee: 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
    up: 'M5 15l7-7 7 7',
    minus: 'M20 12H4',
    plus: 'M12 4v16m8-8H4',
    x: 'M6 18L18 6M6 6l12 12'
  };
  function ic(name, cls) { return '<svg class="' + (cls || 'x-ic') + '" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + P[name] + '"></path></svg>'; }
  var OWL = '/*OWL*/';
  var PLAY = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8 5v14l11-7z"></path></svg>';
  var EXTI = ic('ext', 'x-ext') + '<span class="x-vh"> (opens in a new tab)</span>';
  function extAttr(url) { return 'href="' + esc(url) + '" target="_blank" rel="noopener noreferrer"'; }
  function ext(url, labelHtml, cls) { return '<a ' + extAttr(url) + (cls ? ' class="' + cls + '"' : '') + '>' + labelHtml + EXTI + '</a>'; }
  function tag(html, grey) { return '<span class="x-tag' + (grey ? ' x-grey' : '') + '">' + html + '</span>'; }
  function autoTag() { return tag(ic('refresh') + 'Automatic', 1); }
  function lockTag() { return tag(ic('lock') + 'Members only', 1); }
  function chip(label, act, arg, on, extra) { return '<button type="button" class="x-chip" data-act="' + act + '" data-arg="' + esc(arg) + '" aria-pressed="' + (on ? 'true' : 'false') + '">' + esc(label) + (extra || '') + '</button>'; }
  function info(icon, text) { return '<div class="conference-info-item">' + ic(icon) + '<span>' + esc(text) + '</span></div>'; }
  /* the marks shown when "Mark what is new" is switched on */
  function mk(kind) { return S.mark ? '<span class="x-mark' + (kind === 'idea' ? ' x-idea' : '') + '">' + (kind === 'idea' ? 'Idea' : 'New') + '</span>' : ''; }
  /* a note or a control that belongs to the mockup, not to the site */
  function demo(html) { return '<div class="x-demo"><span class="x-demo-tag">Mockup</span><div>' + html + '</div></div>'; }
  function crumbs(list) {
    return '<ol class="x-crumbs" aria-label="Breadcrumb">' + list.map(function (c) {
      return '<li>' + (c[1] ? '<a href="' + c[1] + '">' + esc(c[0]) + '</a>' : '<span aria-current="page">' + esc(c[0]) + '</span>') + '</li>';
    }).join('') + '</ol>';
  }
  function pageHero(o) {
    return '<section class="hero-section x-short ' + (o.cls || '') + '"><div class="container-ern"><div style="max-width:64rem">' +
      (o.crumbs ? crumbs(o.crumbs) : '') + '<h1 tabindex="-1">' + o.title + '</h1>' + (o.text ? '<p>' + o.text + '</p>' : '') + (o.extra || '') + '</div></div></section>';
  }
  function subnav(items) {
    return '<nav class="x-subnav" aria-label="On this page"><div class="container-ern"><span class="x-subnav-label">On this page</span>' + items.map(function (it) {
      return '<button type="button" data-act="scrollto" data-arg="sec-' + it[0] + '" data-spy="sec-' + it[0] + '">' + esc(it[1]) + '</button>';
    }).join('') + '</div></nav>';
  }
  function tabsRow(act, tabs, cur, label) {
    return '<div class="x-tabs" role="tablist" aria-label="' + esc(label) + '">' + tabs.map(function (t) {
      return '<button type="button" role="tab" class="x-tab" data-act="' + act + '" data-arg="' + t[0] + '" aria-selected="' + (cur === t[0]) + '"' + (cur === t[0] ? '' : ' tabindex="-1"') + '>' + t[1] + (t[2] != null ? '<small>' + t[2] + '</small>' : '') + (t[3] || '') + '</button>';
    }).join('') + '</div>';
  }
  function paras(list) { return list.map(function (t) { return '<p>' + esc(t) + '</p>'; }).join(''); }
  function initials(name) { return name.split(/\s+/).map(function (w) { return w.charAt(0); }).join('').slice(0, 2).toUpperCase(); }
  var toastTimer;
  function toast(msg) {
    var t = document.getElementById('toast'); t.textContent = msg; t.hidden = false;
    clearTimeout(toastTimer); toastTimer = setTimeout(function () { t.hidden = true; }, 3200);
  }
  function copy(text, done, blocked) {
    var manual = function () { toast(blocked || 'Copying is blocked here. Select the text and copy it.'); };
    try { navigator.clipboard.writeText(text).then(function () { toast(done || 'Copied'); }, manual); } catch (e) { manual(); }
  }

  /* ---------- data preparation ---------- */
  var DAYS = { Tue: 'Tuesday', Wed: 'Wednesday', Thu: 'Thursday' };
  var ABS = {}, PUB = {}, CONF = {};
  D.confs.forEach(function (c) { CONF[c.id] = c; });
  D.abstracts.forEach(function (a) {
    ABS[a.id] = a;
    a._t = fold(a.title); a._k = fold(a.kw.join(' ') + ' ' + a.aus);
    a._h = a._t + ' ' + a._k + ' ' + fold(a.by + ' ' + a.sec.map(function (s) { return s.t; }).join(' '));
  });
  D.pubs.forEach(function (p) {
    PUB[p.id] = p;
    p._t = fold(p.t); p._k = fold(p.au + ' ' + p.field); p._c = p._t.replace(/[^a-z0-9]+/g, '').slice(0, 60);
    p._h = p._t + ' ' + p._k + ' ' + fold(p.v + ' ' + p.y + ' ' + (p.sum || ''));
  });
  var NEWPUBS = D.pubs.filter(function (p) { return p.added; }).sort(function (a, b) { return a.added < b.added ? 1 : -1; });
  var STARTERS = D.pubs.filter(function (p) { return p.starter; });
  var WITHVIDEO = D.abstracts.filter(function (a) { return a.video === 'yes'; }).length;
  var DBN = { all: D.pubs.length + D.abstracts.length, article: 0, book: 0, other: 0, abstract: D.abstracts.length };
  D.pubs.forEach(function (p) { DBN[p.type === 'article' ? 'article' : (p.type === 'book' || p.type === 'chapter' ? 'book' : 'other')]++; });
  var DAYN = { all: D.abstracts.length, Tue: 0, Wed: 0, Thu: 0, online: 0 };
  D.abstracts.forEach(function (a) { if (a.fmt === 'online') DAYN.online++; else if (a.s) DAYN[a.s.day]++; });
  var SEP = '<span class="x-chip-sep" aria-hidden="true"></span>';
  function aid(prefix) { var a = D.abstracts.filter(function (x) { return x.title.indexOf(prefix) === 0; })[0]; return a ? a.id : ''; }
  function score(item, ts) {
    var s = 0;
    for (var i = 0; i < ts.length; i++) {
      var t = ts[i];
      if (item._h.indexOf(t) < 0) return 0;
      s += item._t.indexOf(t) >= 0 ? 3 : (item._k.indexOf(t) >= 0 ? 2 : 1);
    }
    return s;
  }
  function sessionLine(a, long) {
    if (a.fmt === 'online') return 'Pre-recorded talk';
    if (!a.s) return '';
    var d = long ? DAYS[a.s.day] + ', ' + a.s.date + ' September 2026' : a.s.day + ' ' + a.s.date + ' Sep';
    return 'Session ' + a.s.no + ' · ' + d + ' · ' + a.s.time + ' · ' + a.s.hall;
  }
  var TYPE = { article: 'Journal article', book: 'Book', chapter: 'Book chapter', report: 'Report', proceedings: 'Conference paper' };
  function savedCount() { return Object.keys(S.saved).filter(function (k) { return S.saved[k]; }).length; }
  function agendaCount() { return Object.keys(S.agenda).filter(function (k) { return S.agenda[k]; }).length; }
  function savedTotal() { return savedCount() + Object.keys(S.myList).filter(function (k) { return S.myList[k]; }).length; }
  function whereSaved() { return S.user ? '' : ' It is kept in this browser. Sign in to keep it in your account.'; }
  function liveBar() {
    if (!S.live) return '';
    return '<div class="x-livebar"><div class="container-ern"><span class="x-livetag"><span class="x-dot"></span>Live</span><span>ERN is live<span class="x-wide"> on Twitch: ' + esc(D.liveDay[0].t) + '</span></span>' + (current() === 'live' ? '' : '<a href="#live">Watch now</a>') + '</div></div>';
  }
  function goalsList() {
    return '<div class="goals-list">' + D.goals.map(function (g) {
      return '<div class="goal-item"><div class="goal-item-content"><div class="goal-icon">' + ic(g[0]) + '</div><div><h3>' + esc(g[1]) + '</h3><p>' + esc(g[2]) + '</p></div></div></div>';
    }).join('') + '</div>';
  }
  function involvedCards() {
    var A = D.involved.actions;
    return '<a href="#join" class="action-card focus-ring"><div class="action-icon">' + ic(A[0][0]) + '</div><h3>' + esc(A[0][1]) + mk() + '</h3><p>' + esc(A[0][2]) + '</p><span class="action-link">Apply online ' + ic('arrow', 'x-ic-sm') + '</span></a>' +
      '<a href="#involved" data-act="gosec" data-arg="involved|sec-ways" class="action-card focus-ring"><div class="action-icon">' + ic(A[1][0]) + '</div><h3>' + esc(A[1][1]) + '</h3><p>' + esc(A[1][2]) + '</p><span class="action-link">Ways to participate ' + ic('arrow', 'x-ic-sm') + '</span></a>' +
      '<a ' + extAttr(D.involved.discord) + ' class="action-card focus-ring"><div class="action-icon">' + ic(A[2][0]) + '</div><h3>' + esc(A[2][1]) + '</h3><p>' + esc(A[2][2]) + '</p><span class="action-link">Open Discord' + EXTI + '</span></a>';
  }
