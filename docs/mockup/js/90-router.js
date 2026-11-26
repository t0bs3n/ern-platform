
  /* ---------- router ---------- */
  function notFound() {
    return pageHero({ title: 'This page is not part of the mockup' }) + '<div class="container-ern content-wrap"><p>The address does not match a page here.</p><a href="#home" class="btn btn-primary">Back to the home page</a></div>';
  }
  var ROUTES = { home: home, news: news, conferences: conferences, ernc27: ernc27, ernc26: ernc26, database: database, live: live, podcast: podcast, about: about,
    involved: involved, join: join, lectures: lectures, conduct: conduct, concern: concern, imprint: imprint, members: members, guide: guide, admin: admin, course: course, saved: saved, submission: submission, events: events, propose: propose, mylectures: mylectures };
  var NAVOF = { ernc27: 'conferences', ernc26: 'conferences', join: 'involved', admin: 'members', course: 'lectures', submission: 'conferences', propose: 'lectures', mylectures: 'lectures' };
  D.confs.forEach(function (c) { if (!c.html) { ROUTES[c.id] = function () { return confSummary(c.id); }; NAVOF[c.id] = 'conferences'; } });
  var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function toTop() { try { window.scrollTo({ top: 0, behavior: 'instant' }); } catch (e) { window.scrollTo(0, 0); } }
  function reveal(el, block, smooth) { try { el.scrollIntoView({ behavior: smooth && !calm ? 'smooth' : 'instant', block: block || 'start' }); } catch (e) { el.scrollIntoView(); } }
  function each(sel, fn) { Array.prototype.forEach.call(document.querySelectorAll(sel), fn); }
  function current() { return (location.hash || '#home').slice(1); }
  function render(keepScroll) {
    var h = current(), isAbs = h.indexOf('a-') === 0 && !!ABS[h], isLec = h.indexOf('l-') === 0 && !!ABS[h.slice(2)], isPub = h.indexOf('p-') === 0 && !!PUB[h], lecN = /^lec-[1-6]$/.test(h) ? Number(h.slice(4)) - 1 : -1, who = h.indexOf('r-') === 0 && D.people.filter(function (p) { return slug(p.name) === h.slice(2); })[0];
    view.classList.toggle('x-still', !!keepScroll);
    view.innerHTML = isAbs ? abstractView(h) : isLec ? lectureView(h.slice(2)) : isPub ? pubView(h) : lecN >= 0 ? lecPage(lecN) : who ? researcher(who) : (ROUTES[h] || notFound)();
    var nav = isAbs ? 'conferences' : isLec ? 'lectures' : isPub ? 'database' : lecN >= 0 ? 'lectures' : who ? 'members' : (NAVOF[h] || h);
    each('[data-nav]', function (li) {
      var on = li.getAttribute('data-nav') === nav; li.classList.toggle('current-menu-item', on);
      var a = li.querySelector('a'); if (on) a.setAttribute('aria-current', 'page'); else a.removeAttribute('aria-current');
    });
    each('[data-livedot]', function (d) { d.hidden = !S.live; });
    each('[data-mark]', function (d) { d.hidden = !S.mark; });
    each('[data-memberbtn]', function (b) { if (h === 'members' || h === 'admin' || who) b.setAttribute('aria-current', 'page'); else b.removeAttribute('aria-current'); b.innerHTML = (S.user ? '<span class="x-ini" aria-hidden="true">' + esc(S.me.ini) + '</span>' : '') + 'Member area' + (S.user ? '<span class="x-vh"> (signed in as ' + esc(S.me.name) + ')</span>' : ''); });
    var nSaved = savedTotal();
    each('[data-savedlink]', function (b) { b.innerHTML = ic('save') + (nSaved ? '<span class="x-count">' + nSaved + '</span>' : '') + '<span class="x-vh">Saved library' + (nSaved ? ', ' + nSaved + ' items' : '') + '</span>'; if (h === 'saved') b.setAttribute('aria-current', 'page'); else b.removeAttribute('aria-current'); });
    each('[data-savedtext]', function (b) { b.textContent = 'Saved library' + (nSaved ? ' (' + nSaved + ')' : ''); });
    each('.x-tabs [aria-selected=true]', function (t) { var r = t.parentNode; if (r.scrollWidth > r.clientWidth) r.scrollLeft = Math.max(0, t.offsetLeft - r.offsetLeft - (r.clientWidth - t.offsetWidth) / 2); });
    closeMenu();
    if (!keepScroll) {
      toTop();
      var t = S.pendingScroll && document.getElementById(S.pendingScroll);
      if (t) reveal(t);
      if (h === 'database' && S.dbOpen) { var row = document.getElementById('row-' + S.dbOpen); if (row) reveal(row, 'center'); }
    }
    S.pendingScroll = '';
    fillSub(); spy(); pdfMount();
  }
  function render2() { var n = savedTotal(); each('[data-savedlink]', function (b) { b.innerHTML = ic('save') + (n ? '<span class="x-count">' + n + '</span>' : '') + '<span class="x-vh">Saved library' + (n ? ', ' + n + ' items' : '') + '</span>'; }); each('[data-savedtext]', function (b) { b.textContent = 'Saved library' + (n ? ' (' + n + ')' : ''); }); }
  var DL = null;
  if (window.claude && window.claude.use) window.claude.use('downloads').then(function (d) { DL = d; }, function () { DL = null; });
  function saveFile(name, data) {
    if (!DL) { toast('Saving files is not possible in this view. Use “Look inside” to read and copy each file.'); return; }
    DL.save({ filename: name, data: data }).then(function () { toast('Saved: ' + name); }, function (e) { toast(e && e.code === 'declined' ? 'Not saved.' : 'The file could not be saved here. Use “Look inside” to read and copy it.'); });
  }
  function ueKeep() {
    var f = document.getElementById('unit-editor'); if (!f) return; var id = f.getAttribute('data-id'), st = S.adm.unit[id] = S.adm.unit[id] || { ver: 0, hist: [] };
    st.draft = st.draft || { at: nowTime() };
    st.draft.out = f.querySelector('#ue-out').value.split('\n').map(function (x) { return x.trim(); }).filter(Boolean); st.draft.slides = f.querySelector('#ue-slides').checked; st.draft.url = f.querySelector('#ue-url').value.trim(); st.draft.rec = Math.min(180, Math.max(1, Math.round(Number(f.querySelector('#ue-rec').value) || 15)));
    st.draft.ch = f.querySelector('#ue-ch').value.split('\n').map(function (x) { var m = x.trim().match(/^(\d{1,3}:\d{2})\s+(.+)$/); return m ? [m[1], m[2]] : null; }).filter(Boolean);
  }
  function go(h) { if (current() === h) render(); else location.hash = h; }
  function part(id, html) { var el = document.getElementById(id); if (el) el.innerHTML = html; }
  function focusHeading() { var h1 = view.querySelector('h1'); if (h1) h1.focus({ preventScroll: true }); }
  function spy() {
    var btns = document.querySelectorAll('[data-spy]'); if (!btns.length) return;
    var line = document.getElementById('site-header').getBoundingClientRect().bottom + 90, cur = '';
    Array.prototype.forEach.call(btns, function (b) { var t = document.getElementById(b.getAttribute('data-spy')); if (t && t.getBoundingClientRect().top <= line) cur = b.getAttribute('data-spy'); });
    Array.prototype.forEach.call(btns, function (b) {
      var on = b.getAttribute('data-spy') === cur, was = b.getAttribute('aria-current') === 'true';
      if (on && !was) { b.setAttribute('aria-current', 'true'); var row = b.parentNode; if (row.scrollWidth > row.clientWidth) row.scrollLeft = Math.max(0, b.offsetLeft - row.clientWidth / 2 + b.offsetWidth / 2); }
      else if (!on && was) b.removeAttribute('aria-current');
    });
  }
  function bookReset() { S.bookQ = ''; S.bookKw = ''; S.bookDay = 'all'; S.bookVideo = false; S.bookSaved = false; }
  function dbReset() { S.dbQ = ''; S.dbType = 'all'; S.dbNew = false; S.dbList = ''; S.dbField = ''; S.dbYears = ''; S.dbSort = 'new'; S.dbOA = false; S.dbAuthor = ''; S.dbOpen = ''; S.dbBib = ''; S.dbLimit = 20; S.dbView = 'search'; }
  function bookParts() { part('book-controls', bookControls()); part('book-results', bookResults()); }
  function dbParts() { part('db-controls', dbControls()); part('db-results', dbResults()); }

  /* ---------- actions: what a click does ---------- */
  var ACT = {
    toast: function (arg) { toast(arg); return false; },
    skip: function () { view.focus({ preventScroll: true }); reveal(view); },
    mark: function () {
      S.mark = !S.mark;
      document.getElementById('mark-toggle').setAttribute('aria-checked', S.mark ? 'true' : 'false');
      document.getElementById('mock-legend').hidden = !S.mark;
      render(true); toast(S.mark ? 'New parts and suggestions are marked now.' : 'Marks are off.');
    },
    scrollto: function (arg) { var el = document.getElementById(arg); if (el) reveal(el, 'start', true); },
    gosec: function (arg) {
      var p = arg.split('|'), el;
      if (current() === p[0] && (el = document.getElementById(p[1]))) reveal(el, 'start', true);
      else { S.pendingScroll = p[1]; go(p[0]); }
      return false;
    },
    presssrc: function (arg) { S.pressSrc = arg; part('mem-body', memBody()); },
    memgo: function (arg) { S.memTab = arg; },
    openpub: function (arg) { dbReset(); S.dbOpen = arg; S.dbLimit = 200; go('database'); return false; },
    dbopen: function (arg) { S.dbOpen = S.dbOpen === arg ? '' : arg; S.dbBib = ''; part('db-results', dbResults()); },
    dbtype: function (arg) { S.dbType = arg; if (arg === 'abstract') S.dbNew = false; S.dbLimit = 20; dbParts(); },
    dbnew: function () { S.dbNew = !S.dbNew; if (S.dbNew && S.dbType === 'abstract') S.dbType = 'all'; S.dbLimit = 20; dbParts(); },
    dblist: function (arg) { S.dbView = 'lists'; S.dbList = arg; S.dbOpen = ''; S.dbBib = ''; if (current() === 'database') { render(true); toTop(); } else go('database'); return false; },
    dbview: function (arg) { S.dbView = arg; S.dbList = ''; S.dbOpen = ''; render(true); },
    dbfilters: function () { S.dbFilters = !S.dbFilters; dbParts(); },
    dboa: function () { S.dbOA = !S.dbOA; S.dbLimit = 20; dbParts(); },
    dbauthor: function (arg) { dbReset(); S.dbAuthor = arg; go('database'); return false; },
    mylist: function (arg) { S.myList[arg] = !S.myList[arg]; if (current() === 'database') { dbParts(); render2(); } else render(true); toast(S.myList[arg] ? 'Saved to your library.' + whereSaved() : 'Removed from your library.'); },
    agenda: function (arg) { S.agenda[arg] = !S.agenda[arg]; render(true); toast(S.agenda[arg] ? 'Added to your agenda.' : 'Removed from your agenda.'); },
    copylink: function (arg) { copy('https://esportsresearch.net/' + arg, 'The link is copied. It is an example address.', 'Copying is blocked here. The address would be esportsresearch.net/' + arg + '.'); },
    copysaved: function () { copy(Object.keys(S.myList).filter(function (k) { return S.myList[k] && PUB[k]; }).map(function (k) { return apa(PUB[k]); }).join('\n'), 'Your saved publications are copied as references.', 'Copying is blocked here. Open the publications and copy them one by one.'); },
    savesearch: function () { S.searches.unshift({ q: S.dbQ, type: S.dbType, field: S.dbField, years: S.dbYears, oa: S.dbOA, author: S.dbAuthor, freq: 'Every week' }); render2(); toast('Search saved in your library.' + (S.user ? ' You get an e-mail when new entries match.' : ' Sign in to get an e-mail when new entries match.')); },
    runsearch: function (arg) { var x = S.searches[Number(arg)]; dbReset(); S.dbQ = x.q; S.dbType = x.type; S.dbField = x.field; S.dbYears = x.years; S.dbOA = x.oa; S.dbAuthor = x.author; S.dbFilters = !!(x.field || x.years || x.oa || x.author); go('database'); },
    rmsearch: function (arg) { S.searches.splice(Number(arg), 1); render(true); },
    askdb: function (arg) { askOpen(false); dbReset(); S.dbQ = arg; go('database'); },
    loginmode: function (arg) { S.loginMode = arg; render(true); var f = document.querySelector('#login-mail, #reset-mail'); if (f) f.focus(); },
    copylist: function () { copy(dbAll().filter(function (r) { return r.p; }).map(function (r) { return apa(r.p); }).join('\n'), 'The list is copied as references.', 'Copying is blocked here. Open the entries and copy them one by one.'); },
    trgo: function (arg) { var p = arg.split('|'); S.playing = p[0]; S.playAt = Number(p[1]); S.lecTab = 'watch'; render(true); var v = document.querySelector('.x-video'); if (v) reveal(v, 'center', true); toast('The recording jumps to ' + mmss(S.playAt) + '.'); },
    lectab: function (arg) { S.lecTab = arg; render(true); },
    tuready: function () { S.tuReady = !S.tuReady; part('tu-list', unitCards()); },
    planlen: function (arg) { S.planLen = arg; render(true); },
    matopen: function () { S.matOpen = !S.matOpen; render(true); var m = document.getElementById('mat-box'); if (m) reveal(m, 'nearest', true); },
    lecpreview: function () { S.lecTab = 'watch'; S.playing = current().slice(2); render(true); var v = document.querySelector('.x-video'); if (v) reveal(v, 'center', true); },
    ltiview: function () { S.lti = !S.lti; S.lecTab = 'watch'; S.matOpen = false; render(true); toTop(); toast(S.lti ? 'This is what a student sees who opened the unit from a course.' : 'Back to the open view.'); },
    propreset: function () { S.proposed = ''; render(); },
    admlecgo: function () { S.user = true; S.admin = true; S.admTab = 'teaching'; S.admLec = 'proposals'; },
    admlec: function (arg) { S.admLec = arg; S.adm.lecOpen = ''; render(true); },
    admprop: function (arg) { S.adm.lecOpen = S.adm.lecOpen === arg ? '' : arg; render(true); var d = document.getElementById('prop-detail'); if (d) reveal(d, 'nearest', true); },
    wfnext: function (arg) { var x = PROPS().filter(function (q) { return q.id === arg; })[0], cur = stepOf(x); if (cur < WF.length - 1) S.adm.wf[arg] = cur + 1; if (S.adm.wf[arg] >= 3 && S.adm.slot[arg] == null) S.adm.slot[arg] = freeSlot(arg); render(true); toast('Moved to: ' + WF[S.adm.wf[arg]] + '.' + (S.adm.wf[arg] === 3 ? ' It is in the draft programme now, not public yet.' : '')); },
    wfback: function (arg) { var cur = stepOf(PROPS().filter(function (q) { return q.id === arg; })[0]); if (cur > 0) S.adm.wf[arg] = cur - 1; if (S.adm.wf[arg] < 3) delete S.adm.slot[arg]; render(true); },
    wfout: function (arg) { var p = arg.split('|'); if (p[1]) S.adm.out[p[0]] = p[1]; else delete S.adm.out[p[0]]; render(true); toast(p[1] ? 'Outcome set: ' + p[1] + '. The speaker sees it in “My lectures”.' : 'The proposal is open again.'); },
    progpub: function () { S.adm.prog = draftProg(); S.adm.progPrev = false; render(true); toast('Published. The Teaching page and the events calendar show the new programme.'); },
    lecstage: function (arg) { S.lecStage = arg; render(true); },
    admlecgo2: function (arg) { S.admTab = 'teaching'; S.admLec = arg; S.adm.lecOpen = ''; render(true); toTop(); },
    report: function (arg) { S.report = S.report === arg ? '' : arg; render(true); var f = document.getElementById('rep-in'); if (f) f.focus(); },
    activate: function () { S.loginMode = 'activate'; },
    progprev: function () { S.adm.progPrev = !S.adm.progPrev; render(true); },
    unitopen: function (arg) { S.adm.unitOpen = S.adm.unitOpen === arg ? '' : arg; S.adm.unitPrev = false; render(true); var e = document.getElementById('unit-editor'); if (e) reveal(e, 'nearest', true); },
    unitprev: function () { ueKeep(); S.adm.unitPrev = !S.adm.unitPrev; render(true); },
    uefile: function (arg) { var p = arg.split('|'); ueKeep(); var st = S.adm.unit[p[0]]; st.draft.files = st.draft.files || {}; st.draft.files[p[1]] = nowTime(); render(true); toast('Not uploaded in this mockup. The new file would sit in the draft until you publish.'); },
    unitpub: function (arg) { var st = S.adm.unit[arg]; if (!st || !st.draft) return; st.out = st.draft.out; st.slides = st.draft.slides; st.rec = st.draft.rec; st.ch = st.draft.ch; st.url = st.draft.url; st.ver += 1; st.hist.unshift('1.' + st.ver + ' published on ' + todayStamp()); st.draft = null; S.adm.unitPrev = false; render(true); toast('Version 1.' + st.ver + ' is published. Teachers see it on the unit page and in the package.'); },
    pkglook: function (arg) { S.pkgOpen = S.pkgOpen === Number(arg) ? -1 : Number(arg); render(true); },
    pkgsave: function (arg) { var a = ABS[current().slice(2)], pk = pkgFiles(a), n = (Number(arg) + 1) * 2; saveFile(pkgName(a) + '-' + pk[n], pk[n + 1]); },
    pkgzip: function () { var a = ABS[current().slice(2)], pk = pkgFiles(a), files = []; for (var n = 0; n < pk.length; n += 2) files.push({ name: pkgName(a) + '/' + pk[n], text: pk[n + 1] }); saveFile(pkgName(a) + '.zip', new Blob(zipParts(files))); },
    wsup: function (arg) { S.ws.up[arg] = nowTime(); render(true); toast('Not uploaded in this mockup. The file would wait here until you submit it for review.'); },
    wssend: function (arg) { var any = Object.keys(S.ws.up).some(function (k) { return k.indexOf(arg + '|') === 0; }); if (!any) { toast('Upload or replace a file first.'); return; } Object.keys(S.ws.up).forEach(function (k) { if (k.indexOf(arg + '|') === 0) delete S.ws.up[k]; }); S.ws.sent[arg] = todayStamp(); render(true); toast('Sent for review. An editor checks your changes before they go public.'); },
    wsaccept: function (arg) { delete S.ws.sent[arg]; render(true); toast('Accepted. The materials of the speaker are part of the unit now.'); },
    wswithdraw: function (arg) { S.adm.out[arg] = 'Withdrawn'; render(true); toast('Your proposal is withdrawn.'); },
    wsresend: function (arg) { delete S.adm.out[arg]; S.adm.wf[arg] = 1; render(true); toast('Sent again. The lecture team reviews the revised proposal.'); },
    copyq: function (arg) { copy(qText(ABS[arg]), 'Questions and prompts are copied.', 'Copying is blocked here. Select the questions and copy them.'); },
    copylec: function (arg) { copy('https://esportsresearch.net/lectures/' + arg.slice(2, 40), 'The link is copied. It is an example address.', 'Copying is blocked here.'); },
    copycourse: function () { copy(courseText(), 'The course description is copied.', 'Copying is blocked here. Open the learning objectives and select the text.'); },
    dbstarter: function () { dbReset(); S.dbView = 'lists'; S.dbList = 'starter'; },
    dbmore: function () { S.dbLimit += 30; part('db-results', dbResults()); },
    dbclear: function () { dbReset(); var i = document.getElementById('db-q'); if (i) { i.value = ''; i.focus(); } dbParts(); },
    copypub: function (arg) { copy(apa(PUB[arg]), 'Citation copied', 'Copying is blocked here. Select the citation and copy it.'); },
    copybib: function (arg) {
      S.dbBib = S.dbBib === arg ? '' : arg; part('db-results', dbResults());
      if (current() !== 'database') render(true);
      if (S.dbBib) copy(bibtex(PUB[arg]), 'BibTeX entry copied. It is also shown below.', 'The BibTeX entry is shown below. Select it and copy it.');
    },
    copycite: function () { copy(document.getElementById('cite-text').textContent, 'Citation copied', 'Copying is blocked here. Select the citation and copy it.'); },
    c27tab: function (arg, el) { S.c27Tab = arg; if (el.tagName !== 'A') { render(true); if (!el.closest('[role=tablist]')) toTop(); } },
    subreset: function () { S.sub = { vals: {}, stage: 'form', savedAt: '', offline: false, error: '', revising: '' }; render(); },
    suboffline: function () { S.sub.offline = !S.sub.offline; S.sub.error = ''; render(true); toast(S.sub.offline ? 'The mockup now pretends the connection is lost. Try to save or submit.' : 'The connection is back.'); },
    subdraft: function () {
      var f = document.querySelector('form[data-form=submit]'); if (!validate(f, [['sub-mail', 'Enter your e-mail address. The link to your draft goes there.', isMail]])) return;
      if (S.sub.offline) { S.sub.error = 'Saving the draft'; render(true); reveal(document.getElementById('sub-error'), 'center'); return; }
      S.sub.error = ''; S.sub.savedAt = nowTime(); S.sub.stage = 'saved'; render(true); toTop();
    },
    subresume: function () { S.sub.stage = 'form'; S.c27Tab = 'submit'; if (current() === 'ernc27') { render(true); toTop(); } else go('ernc27'); toast('Your draft is open again, as you left it.'); },
    subedit: function () { S.sub.stage = 'form'; S.sub.error = ''; render(true); toTop(); },
    subretry: function () { if (S.sub.offline) { toast('Still no connection. Your text is safe. Switch the connection back on in the mockup note.'); return; } S.sub.error = ''; render(true); toast('You are back online. Save or submit again.'); },
    subfinal: function () {
      if (S.sub.offline) { S.sub.error = 'Submitting'; render(true); reveal(document.getElementById('sub-error'), 'center'); return; }
      var v = S.sub.vals, old = S.subs.filter(function (x) { return x.no === S.sub.revising; })[0];
      if (old) { old.title = v['sub-title']; old.revised = true; } else S.subs.push({ title: v['sub-title'], format: v['sub-format'], strand: v['sub-strand'], mail: v['sub-mail'], no: 'ERNC27-0' + (42 + S.subs.length) });
      S.sub.stage = 'done'; S.sub.error = ''; S.sub.revising = ''; render(true); toTop();
    },
    subview: function (arg) { S.subView = arg; render(true); },
    subrevise: function () { var x = S.subs[S.subs.length - 1]; S.sub.revising = x.no; S.sub.stage = 'form'; S.c27Tab = 'submit'; go('ernc27'); },
    evtype: function (arg) { S.evType = arg; render(true); },
    admsend: function (arg) { var d = S.adm.dec[arg]; if (!d) return; d.sent = todayStamp(); (S.adm.hist[arg] = S.adm.hist[arg] || []).push(d.sent + ': “' + d.res + '” sent to the corresponding author'); render(true); toast('Sent to the corresponding author.'); },
    admsendall: function () { var n = 0; Object.keys(S.adm.dec).forEach(function (k) { var d = S.adm.dec[k]; if (d && !d.sent) { d.sent = todayStamp(); (S.adm.hist[k] = S.adm.hist[k] || []).push(d.sent + ': “' + d.res + '” sent to the corresponding author'); n++; } }); render(true); toast(n ? n + ' decision' + (n > 1 ? 's' : '') + ' sent.' : 'There is no saved decision waiting to be sent.'); },
    dirclear: function () { S.dirTopic = S.dirCountry = S.dirRole = S.dirOpen = ''; S.memQ = ''; render(true); },
    sendmsg: function () { toast('Not sent in this mockup. ERN would forward your message by e-mail.'); },
    progday: function (arg) { S.progDay = arg; render(true); },
    progmine: function () { S.progMine = !S.progMine; render(true); },
    conftab: function (arg, el) { S.confTab = arg; if (el.tagName !== 'A') render(true); },
    pasttab: function (arg, el) { var p = arg.split('|'); S.pastTab[p[0]] = p[1]; if (el.tagName !== 'A') render(true); },
    pdfstep: function (arg) { pdfJump(Math.min(PV.n || 1, Math.max(1, PV.cur + Number(arg)))); },
    pdfgo: function (arg) { pdfJump(Number(arg)); var box = document.getElementById('pdf-pages'); if (box) { reveal(document.getElementById('pdf'), 'nearest'); box.focus({ preventScroll: true }); } return false; },
    pdfzoom: function (arg) {
      var z = Math.min(ZOOMS.length - 1, Math.max(0, PV.zoom + Number(arg))); if (z === PV.zoom || !PV.doc) return;
      PV.zoom = z; var col = document.getElementById('pdf-col'); if (!col) return;
      col.style.setProperty('--z', ZOOMS[z]); document.getElementById('pdf-zoom').textContent = Math.round(ZOOMS[z] * 100) + '%';
      var keep = PV.cur; pdfJump(keep);
      for (var k = Math.max(1, keep - 1); k <= Math.min(PV.n, keep + 2); k++) pdfDraw(k, PV.gen);
    },
    pdfclear: function () { PV.q = ''; var i = document.getElementById('pdf-q'); if (i) { i.value = ''; i.focus(); } pdfHits(); },
    confvideos: function () { bookReset(); S.confTab = 'book'; S.bookVideo = true; },
    bookday: function (arg) { S.bookDay = arg; bookParts(); },
    bookvideo: function () { S.bookVideo = !S.bookVideo; bookParts(); },
    booksaved: function () { S.bookSaved = !S.bookSaved; bookParts(); },
    bookkw: function (arg) { bookReset(); S.bookKw = arg; S.confTab = 'book'; go('ernc26'); return false; },
    bookclear: function () { bookReset(); var i = document.getElementById('book-q'); if (i) { i.value = ''; i.focus(); } bookParts(); },
    save: function (arg) {
      S.saved[arg] = !S.saved[arg];
      if (current() === 'ernc26' && S.confTab === 'book') { bookParts(); render2(); } else render(true);
      toast(S.saved[arg] ? 'Saved to your library.' + whereSaved() : 'Removed from your library.');
    },
    play: function (arg) { S.playing = arg; S.playAt = 0; if (arg && S.lti && !S.attended[arg]) S.attended[arg] = todayStamp(); render(true); },
    setlive: function (arg) { S.live = arg === '1'; render(true); toast(S.live ? 'The stream is live now. See the banner and the dot in the menu.' : 'Back to the offline view.'); },
    copydc: function (arg) { copy(arg, 'Discord name copied: ' + arg, 'Copying is blocked here. The Discord name is ' + arg + '.'); },
    adminin: function (arg, el) { S.user = true; S.admin = true; if (el.tagName !== 'A') render(); },
    admsub: function (arg) { S.adm.open = S.adm.open === arg ? '' : arg; render(true); var d = document.getElementById('adm-detail'); if (d) reveal(d, 'nearest', true); },
    admundo: function (arg) { delete S.adm.dec[arg]; render(true); },
    admtake: function () { var t = document.getElementById('adm-fb'), x = admSubs().filter(function (y) { return y.no === S.adm.open; })[0]; if (!t) return; if (!x.rev) { toast('There is no review yet.'); return; } t.value = REVIEWS.slice(0, x.rev).map(function (r) { return r[0] + ': ' + r[2]; }).join('\n\n'); t.focus(); },
    admtab: function (arg, el) { S.admTab = arg; S.adm.add = ''; S.adm.lecOpen = ''; if (el.tagName !== 'A') render(true); },
    admrole: function (arg) { S.adm.edit = S.adm.edit === arg ? '' : arg; render(true); var d = document.getElementById('adm-rolebox'); if (d) reveal(d, 'nearest', true); },
    admadd: function (arg) { S.adm.add = S.adm.add === arg ? '' : arg; render(true); var f = document.querySelector('#nc-name, #ex-title'); if (f) f.focus(); },
    admunassign: function (arg) { delete S.adm.assign[arg]; render(true); },
    admapp: function (arg) { var p = arg.split('|'); S.adm.apps[p[0]] = p[1]; render(true); if (p[1]) toast(p[1] === 'ok' ? 'Approved. The welcome e-mail with the sign-in would go out now.' : 'Declined.'); },
    admqueue: function (arg) { var p = arg.split('|'); S.adm.queue[p[0]] = p[1]; render(true); if (p[1]) toast(p[1] === 'ok' ? 'Published in the database.' : 'Discarded.'); },
    login: function () { var n = savedTotal() + S.searches.length; S.user = true; S.loginMode = ''; go('members'); toast('Signed in as a demo member.' + (n ? ' Your ' + n + ' saved items are now in your account.' : '')); return false; },
    logout: function () { S.user = null; S.admin = false; S.memTab = 'overview'; S.dbNew = false; render(); toast('Signed out'); },
    memtab: function (arg) { S.memTab = arg; render(true); var t = document.querySelector('.x-many [aria-selected=true]'); if (t && t.parentNode.scrollWidth > t.parentNode.clientWidth) t.parentNode.scrollLeft = Math.max(0, t.offsetLeft - 24); },
    joinreset: function () { S.joined = ''; render(); },
    concernreset: function () { S.concernSent = false; render(); },
    askopen: function () { askOpen(true); return false; },
    askclose: function () { askOpen(false); },
    asksugg: function (arg) { ask(arg); },
    askgo: function (arg) {
      if (window.innerWidth < 640) askOpen(false);
      if (ABS[arg]) go(arg); else ACT.openpub(arg);
    },
    menu: function () {
      var m = document.getElementById('mobile-menu');
      if (m.classList.contains('active')) closeMenu();
      else { m.classList.add('active'); document.getElementById('site-header').classList.add('scrolled'); document.getElementById('mobile-menu-toggle').setAttribute('aria-expanded', 'true'); }
    }
  };
  function closeMenu() {
    document.getElementById('mobile-menu').classList.remove('active');
    document.getElementById('mobile-menu-toggle').setAttribute('aria-expanded', 'false');
    onScroll();
  }
  var INPUT = {
    bookq: function (v) { S.bookQ = v; bookParts(); },
    dbauthor: function (v) { S.dbAuthor = v.trim(); S.dbLimit = 20; part('db-results', dbResults()); },
    dbq: function (v) { S.dbQ = v; S.dbLimit = 20; S.dbOpen = ''; dbParts(); var i = document.getElementById('db-q'); if (i) i.focus(); },
    memq: function (v) { S.memQ = v; part('mem-results', peopleList()); },
    pdfq: function (v) { PV.q = v; pdfHits(); },
    subw: function (v, el) { var f = el.closest('form'), n = words(f); S.sub.vals[el.id] = el.value; var c = document.getElementById('sub-count'); if (c) { c.innerHTML = '<strong>' + n + '</strong> of 500 words' + (n > 500 ? ', ' + (n - 500) + ' too many' : ''); c.classList.toggle('x-over', n > 500); } },
    notew: function (v) { var c = document.getElementById('note-count'); if (c) c.textContent = (v.trim() ? v.trim().split(/\s+/).length : 0) + ' words'; },
    trq: function (v) { S.trQ = v; var h = current(), a = ABS[h] || ABS[h.slice(2)]; if (a) part('tr-list', trHtml(a)); }
  };
  var FORM = {
    homesearch: function (f) { dbReset(); S.dbQ = f.querySelector('input').value.trim(); go('database'); },
    login: function () { ACT.login(); },
    chat: function (f) { var i = f.querySelector('input'), v = i.value.trim(); if (!v) return; S.chat.push({ u: 'you', m: v }); i.value = ''; part('chat-log', chatLog()); var l = document.getElementById('chat-log'); l.scrollTop = l.scrollHeight; },
    profile: function (f) {
      ['name', 'inst', 'country', 'status', 'interests', 'orcid', 'discord'].forEach(function (k) { S.me[k] = f.elements[k].value.trim() || S.me[k]; });
      ['listed', 'collab', 'dcshow', 'reviewer', 'mentor', 'media', 'present'].forEach(function (k) { S.me[k] = f.elements[k].checked; });
      S.me.ini = initials(S.me.name);
      render(true); toast('Your details are saved.');
    },
    ask: function (f) { var i = f.querySelector('input'); ask(i.value); i.value = ''; },
    pdfgo: function (f) { var n = Math.round(Number(f.querySelector('input').value)); if (n >= 1 && n <= PV.n) { pdfJump(n); document.getElementById('pdf-pages').focus({ preventScroll: true }); } else { f.querySelector('input').value = PV.cur; toast('This book has pages 1 to ' + PV.n + '.'); } },
    join: function (f) {
      var need = 'This field is needed.';
      if (!validate(f, [['join-first', need], ['join-last', need], ['join-mail', 'Enter an e-mail address, for example you@university.edu.', isMail], ['join-inst', need], ['join-country', need], ['join-status', 'Please choose one.'], ['join-agree', 'Please confirm that you have read both.']])) return;
      S.joined = f.querySelector('#join-first').value.trim(); render();
    },
    submit: function (f) {
      var need = 'This field is needed.', rules = [['sub-title', need], ['sub-format', 'Please choose one.'], ['sub-strand', 'Please choose one.'], ['sub-name', need], ['sub-mail', 'Enter an e-mail address, for example you@university.edu.', isMail], ['sub-aff', need]];
      for (var k = 0; k < 6; k++) rules.push(['sub-s' + k, 'Every abstract has this section.']);
      rules.push(['sub-kw', 'Enter three to five keywords, separated by commas.', function (v) { var n = v.split(',').filter(function (x) { return x.trim(); }).length; return n >= 3 && n <= 5; }], ['sub-reg', 'Please confirm this.']);
      if (!validate(f, rules)) return;
      if (words(f) > 500) { toast('The abstract has ' + words(f) + ' words. The limit is 500, including references.'); f.querySelector('#sub-s0').focus(); return; }
      S.sub.stage = 'preview'; S.sub.error = ''; render(true); toTop();
    },
    admdec: function (f) {
      if (!validate(f, [['adm-res', 'Please choose the final decision.']])) return;
      var no = f.getAttribute('data-no'), res = f.querySelector('#adm-res').value;
      S.adm.dec[no] = { res: res, fb: f.querySelector('#adm-fb').value.trim(), sent: '' };
      S.adm.status[no] = 'Reviews complete';
      render(true); toast('Decision saved. It is not sent yet.');
    },
    note: function (f) { var v = f.querySelector('textarea').value.trim(); if (!v) { toast('Write a few sentences first.'); f.querySelector('textarea').focus(); return; } S.notes[f.getAttribute('data-id')] = v; if (S.lti) render(true); toast(S.lti ? 'Handed in. Your teacher sees the note in the course.' : 'Your note is saved for this visit.'); },
    propose: function (f) {
      var need = 'This field is needed.';
      if (!validate(f, [['pr-title', need], ['pr-desc', need], ['pr-level', 'Please choose one.'], ['pr-format', 'Please choose one.'], ['pr-name', need], ['pr-mail', 'Enter an e-mail address, for example you@university.edu.', isMail]])) return;
      var v = function (id) { return f.querySelector('#' + id).value.trim(); };
      S.props.push({ id: 'me' + S.props.length, title: v('pr-title'), who: v('pr-name'), level: v('pr-level'), desc: v('pr-desc'), when: v('pr-when'), step: 0, ext: !S.user });
      S.proposed = v('pr-title'); render();
    },
    lecfb: function (f) { var t = f.querySelector('#lec-fb'), id = f.getAttribute('data-id'); if (!t.value.trim()) { t.focus(); toast('Write the feedback first.'); return; } (S.adm.fb[id] = S.adm.fb[id] || []).push([todayStamp(), t.value.trim()]); render(true); toast('Feedback recorded. The speaker reads it in “My lectures”. Internal notes are never sent.'); },
    unitedit: function (f) { ueKeep(); S.adm.unit[f.getAttribute('data-id')].draft.at = nowTime(); render(true); toast('Draft saved. Nothing is public until you publish.'); },
    admroles: function (f) {
      var name = f.getAttribute('data-name'), list = ['Member'];
      Array.prototype.forEach.call(f.querySelectorAll('input:checked:not(:disabled)'), function (i) { list.push(i.value); });
      if (list.indexOf('Final editor') >= 0) D.people.forEach(function (p) { if (p.name !== name) { var r = rolesOf(p.name).filter(function (x) { return x !== 'Final editor'; }); S.adm.roles[p.name] = r; } });
      S.adm.roles[name] = list; S.adm.edit = ''; render(true); toast('Rights saved for ' + name + ': ' + list.join(', ') + '.');
    },
    admassign: function (f) {
      var list = Array.prototype.map.call(f.querySelectorAll('input:checked'), function (i) { return i.value; });
      if (list.length !== 2) { toast('Please choose two reviewers.'); return; }
      S.adm.assign[f.getAttribute('data-no')] = list; S.adm.status[f.getAttribute('data-no')] = 'In review'; render(true); toast('Assigned: ' + list.join(' and ') + '.');
    },
    admconf: function (f) {
      var need = 'This field is needed.';
      if (!validate(f, [['nc-name', need], ['nc-place', need], ['nc-dates', need], ['nc-mode', 'Please choose one.']])) return;
      var v = function (id) { return f.querySelector('#' + id).value.trim(); };
      S.adm.confs.unshift({ name: v('nc-name'), place: v('nc-place'), dates: v('nc-dates') }); S.adm.add = ''; render(true); toast(v('nc-name') + ' is created as a draft.');
    },
    admextra: function (f) {
      if (!validate(f, [['ex-title', 'This field is needed.']])) return;
      var k = f.getAttribute('data-key'); (S.adm.extra[k] = S.adm.extra[k] || []).unshift([f.querySelector('#ex-title').value.trim(), f.querySelector('#ex-second').value.trim()]);
      S.adm.add = ''; render(true); toast('Saved as a draft.');
    },
    rmsg: function (f) { var t = f.querySelector('textarea'); if (!t.value.trim()) { t.focus(); toast('Write a few words first.'); return; } t.value = ''; toast('Not sent in this mockup. ERN would forward your message by e-mail.'); },
    repsend: function (f) { var t = f.querySelector('textarea'); if (!t.value.trim()) { t.focus(); toast('Say in a few words what is wrong.'); return; } S.report = ''; render(true); toast('Thank you. Not sent in this mockup: the Database Lead would get your note.'); },
    activate: function (f) { var a = f.querySelector('#act-pw'), b = f.querySelector('#act-pw2'); if (a.value.length < 8) { a.focus(); toast('Choose a password of at least eight characters.'); return; } if (a.value !== b.value) { b.focus(); toast('The two passwords differ.'); return; } S.user = true; S.loginMode = ''; S.joined = ''; render(); toast('Your account is active. Welcome to the member area.'); },
    pwreset: function (f) { var m = f.querySelector('#reset-mail'); if (!isMail(m.value.trim())) { m.focus(); toast('Enter your e-mail address.'); return; } S.loginMode = 'sent'; render(true); },
    concern: function (f) {
      if (!validate(f, [['con-event', 'Please choose the event or platform.'], ['con-type', 'Please choose the type of concern.'], ['con-text', 'Please describe what happened.']])) return;
      S.concernSent = true; render();
    }
  };
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-act]'); if (!el) return;
    var act = el.getAttribute('data-act'), arg = el.getAttribute('data-arg'), fn = ACT[act]; if (!fn) return;
    var href = el.tagName === 'A' ? el.getAttribute('href') : '';
    var res = fn(arg, el, e);
    if (href && href.charAt(0) === '#') { e.preventDefault(); if (res !== false) go(href.slice(1)); }
    /* a control that was redrawn by its own click keeps the keyboard focus */
    if (!document.activeElement || document.activeElement === document.body) {
      var again = document.querySelector('[data-act="' + act + '"]' + (arg != null ? '[data-arg="' + String(arg).replace(/["\\]/g, '\\$&') + '"]' : ''));
      if (!again) again = document.querySelector('#view [data-act="' + act + '"]');
      if (again) again.focus({ preventScroll: true });
    }
  });
  function keepSub(e) { var t = e.target; if (t.id && t.id.indexOf('sub-') === 0 && t.closest('form[data-form=submit]')) S.sub.vals[t.id] = t.type === 'checkbox' ? t.checked : t.value; }
  document.addEventListener('input', keepSub); document.addEventListener('change', keepSub);
  document.addEventListener('input', function (e) {
    var k = e.target.getAttribute && e.target.getAttribute('data-input'); if (k && INPUT[k]) INPUT[k](e.target.value, e.target);
    var t = e.target; if (t.getAttribute && t.getAttribute('aria-invalid') === 'true' && (t.type === 'checkbox' ? t.checked : t.value.trim())) { t.setAttribute('aria-invalid', 'false'); var er = document.getElementById(t.id + '-err'); if (er) { er.textContent = ''; er.hidden = true; } }
  });
  document.addEventListener('change', function (e) {
    var c = e.target; if (c.getAttribute && c.getAttribute('aria-invalid') === 'true' && (c.type === 'checkbox' ? c.checked : c.value)) { c.setAttribute('aria-invalid', 'false'); var ce = document.getElementById(c.id + '-err'); if (ce) { ce.textContent = ''; ce.hidden = true; } }
    var k = e.target.getAttribute && e.target.getAttribute('data-change'); if (!k) return;
    if (k.indexOf('adm-st-') === 0) { S.adm.status[k.slice(7)] = e.target.value; toast('Status saved: ' + e.target.value + '.'); return; }
    if (k === 'wf-slot') { var d = draftProg(), id = S.adm.lecOpen, to = Number(e.target.value); if (d[to] && d[to] !== id) { toast('This date is taken by another lecture.'); render(true); return; } S.adm.slot[id] = to; render(true); toast('Date changed in the draft programme.'); return; }
    if (k === 'tu-topic' || k === 'tu-level') { S[k === 'tu-topic' ? 'tuTopic' : 'tuLevel'] = e.target.value; part('tu-list', unitCards()); return; }
    if (k.indexOf('dir-') === 0) { S[{ 'dir-topic': 'dirTopic', 'dir-country': 'dirCountry', 'dir-role': 'dirRole', 'dir-open': 'dirOpen' }[k]] = e.target.value; part('mem-results', peopleList()); return; }
    if (k.indexOf('ss-') === 0) { S.searches[Number(k.slice(3))].freq = e.target.value; toast('Saved: ' + e.target.value.toLowerCase() + '.'); return; }
    if (k === 'db-lang') S.dbLang = e.target.value; else if (k === 'db-field') S.dbField = e.target.value; else if (k === 'db-years') S.dbYears = e.target.value; else if (k === 'db-sort') S.dbSort = e.target.value;
    S.dbLimit = 20; dbParts(); var again = document.getElementById(k); if (again) again.focus();
  });
  document.addEventListener('focusin', function (e) { if (e.target.matches && e.target.matches('#view input, #view textarea, #view select')) document.body.classList.add('x-typing'); });
  document.addEventListener('focusout', function () { document.body.classList.remove('x-typing'); });
  document.addEventListener('submit', function (e) {
    var k = e.target.getAttribute('data-form'); if (!k) return;
    e.preventDefault(); if (FORM[k]) FORM[k](e.target);
  });
  document.addEventListener('keydown', function (e) {
    /* tabs: the arrow keys move between them, as in any tab bar */
    if ((e.key === 'ArrowRight' || e.key === 'ArrowLeft') && e.target.getAttribute && e.target.getAttribute('role') === 'tab') {
      var tabs = Array.prototype.slice.call(e.target.parentNode.querySelectorAll('[role=tab]')), i = tabs.indexOf(e.target);
      var to = tabs[(i + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
      e.preventDefault(); to.focus(); to.click(); return;
    }
    if (e.key !== 'Escape') return;
    if (!document.getElementById('ask').hidden) askOpen(false);
    else if (document.getElementById('mobile-menu').classList.contains('active')) { closeMenu(); document.getElementById('mobile-menu-toggle').focus(); }
  });
  var ticking = false;
  function onScroll() {
    var open = document.getElementById('mobile-menu').classList.contains('active');
    document.getElementById('site-header').classList.toggle('scrolled', open || window.scrollY > document.getElementById('mockbar').offsetHeight + 4);
  }
  window.addEventListener('scroll', function () {
    if (ticking) return; ticking = true;
    requestAnimationFrame(function () { ticking = false; onScroll(); spy(); });
  }, { passive: true });
  window.addEventListener('hashchange', function () { S.playing = ''; S.playAt = 0; S.trQ = ''; S.lecTab = 'watch'; S.matOpen = false; S.pkgOpen = -1; render(); focusHeading(); });
  render();
