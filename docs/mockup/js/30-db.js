
  /* ---------- database: search, filters, lists, authors ---------- */
  var LISTS = [
    { id: 'starter', name: 'Starter reading list', note: 'A first reading list for newcomers to esports research, oldest first.', ids: STARTERS.map(function (p) { return p.id; }) },
    { id: 'mental', name: 'Mental health and wellbeing', note: 'Reviews and recent studies on the mental health of players.', ids: ['p-banyai-2019', 'p-leis-2020', 'p-pedraza-2020', 'p-kegelaers-2025', 'p-poulus-sharpe-2025', 'p-poulus-2026', 'p-van-ruysevelt-2026'] },
    { id: 'business', name: 'Esports as a business', note: 'Management, markets and audiences.', ids: ['p-seo-2013', 'p-funk-2018', 'p-hallmann-2018', 'p-scholz-2019', 'p-scholz-2020', 'p-mccauley-2020', 'p-rietz-2023'] }
  ];
  LISTS.forEach(function (l) { l.ids = l.ids.filter(function (id) { return PUB[id]; }); });
  var FIELDS = {}; D.pubs.forEach(function (p) { FIELDS[p.field] = (FIELDS[p.field] || 0) + 1; });
  var YEARS = { '': [0, 9999], recent: [2024, 9999], y20: [2020, 2023], y10: [2010, 2019], early: [0, 2009] };
  function myCount() { return Object.keys(S.myList).filter(function (k) { return S.myList[k]; }).length; }
  function listIds() { if (S.dbList === 'mine') return Object.keys(S.myList).filter(function (k) { return S.myList[k]; }); var l = LISTS.filter(function (x) { return x.id === S.dbList; })[0]; return l ? l.ids : null; }
  function dbFilterN() { return (S.dbField ? 1 : 0) + (S.dbYears ? 1 : 0) + (S.dbOA ? 1 : 0) + (S.dbNew ? 1 : 0) + (S.dbAuthor ? 1 : 0) + (S.dbLang ? 1 : 0); }
  function dbActive() { return !!(S.dbQ || dbFilterN() || S.dbAuthor || S.dbType !== 'all'); }
  function dbAll() {
    var inList = S.dbView === 'lists', ts = inList ? [] : toks(S.dbQ), out = [], ids = inList ? (listIds() || []) : null, yr = YEARS[inList ? '' : S.dbYears], au = inList ? '' : fold(S.dbAuthor);
    if (inList) { ids.forEach(function (id) { var p = PUB[id]; if (p) out.push({ p: p, s: 1, y: p.y, d: '', c: p.cited.length }); }); out.sort(function (x, y) { return S.dbList === 'starter' ? x.y - y.y : y.y - x.y; }); return out; }
    function ok(type) { return S.dbType === 'all' || S.dbType === type; }
    D.pubs.forEach(function (p) {
      var group = p.type === 'article' ? 'article' : (p.type === 'book' || p.type === 'chapter' ? 'book' : 'other');
      if (!ok(group) || (S.dbNew && !p.added) || (S.dbField && p.field !== S.dbField) || (S.dbOA && !p.oa) || p.y < yr[0] || p.y > yr[1]) return;
      if (au && fold(p.au).indexOf(au) < 0) return;
      var s = ts.length ? score(p, ts) : 1; if (s) out.push({ p: p, s: s, y: p.y, d: (S.user && p.added) || '', c: p.cited.length });
    });
    if (!S.dbNew && !S.dbField && !S.dbOA && ok('abstract') && 2026 >= yr[0] && 2026 <= yr[1]) D.abstracts.forEach(function (a) {
      if (au && !a.au.some(function (n) { return fold(n).split(' ').pop() === au; })) return;
      var s = ts.length ? score(a, ts) : 1; if (s) out.push({ a: a, s: s, y: 2026, d: '', c: 0 });
    });
    out.sort(function (x, y) {
      if (ts.length && S.dbSort === 'new') return (y.s - x.s) || (y.y - x.y);
      if (S.dbSort === 'old') return x.y - y.y;
      if (S.dbSort === 'cited') return (y.c - x.c) || (y.y - x.y);
      return y.d > x.d ? 1 : y.d < x.d ? -1 : (y.y - x.y) || (x.a ? 1 : 0) - (y.a ? 1 : 0);
    });
    return out;
  }
  function apa(p) { return p.au + ' (' + p.y + '). ' + p.t + (/[?!.]$/.test(p.t) ? ' ' : '. ') + p.v + '.' + (p.doi ? ' https://doi.org/' + p.doi : ''); }
  function bibtex(p) {
    var kind = { article: 'article', book: 'book', chapter: 'incollection', proceedings: 'inproceedings', report: 'techreport' }[p.type];
    var venue = { article: 'journal', book: 'publisher', chapter: 'booktitle', proceedings: 'booktitle', report: 'institution' }[p.type];
    var key = fold(p.au.split(',')[0]).replace(/[^a-z]/g, '') + p.y;
    var au = p.au.replace(/\.,\s*(&\s*)?/g, '. and ').replace(/\s*&\s*/g, ' and ');
    return '@' + kind + '{' + key + ',\n  author = {' + au + '},\n  title = {' + p.t + '},\n  ' + venue + ' = {' + p.v + '},\n  year = {' + p.y + '}' + (p.doi ? ',\n  doi = {' + p.doi + '}' : '') + '\n}';
  }
  function surnames(p) { return p.au.replace(/\.,\s*(&\s*)?/g, '.|').replace(/\s*&\s*/g, '|').split('|').map(function (n) { return n.split(',')[0].trim(); }).filter(function (n) { return n && n.length < 30; }).slice(0, 6); }
  function pubItem(p, q) {
    var open = S.dbOpen === p.id, isNew = !!(S.user && p.added), mine = !!S.myList[p.id];
    var tags = (isNew ? tag('New') : '') + tag(esc(p.field), 1) + (p.oa ? tag('Open access', 1) : '') + (p.from ? tag('First presented at ERNC26') : '') + (p.cited.length ? tag('Cited in ' + p.cited.length + ' ERNC26 abstract' + (p.cited.length > 1 ? 's' : ''), 1) : '');
    var detail = '';
    if (open) {
      detail = '<div class="x-detail" id="detail-' + p.id + '">' + (p.sum ? '<p>' + esc(p.sum) + '</p>' : '') + '<p class="x-cite" id="cite-' + p.id + '">' + esc(apa(p)) + '</p>' +
        '<div class="x-row" style="margin:.75rem 0"><button type="button" class="btn btn-primary x-btn-sm" data-act="copypub" data-arg="' + p.id + '">Copy citation</button>' +
        '<button type="button" class="btn btn-outline x-btn-sm" data-act="copybib" data-arg="' + p.id + '" aria-expanded="' + (S.dbBib === p.id) + '">BibTeX</button>' +
        '<button type="button" class="btn btn-outline x-btn-sm" data-act="mylist" data-arg="' + p.id + '" aria-pressed="' + mine + '">' + ic('save') + (mine ? 'Saved' : 'Save') + '</button>' +
        (p.doi ? ext('https://doi.org/' + p.doi, 'Open at the publisher', 'btn btn-outline x-btn-sm') : '') + '</div>' +
        (S.dbBib === p.id ? '<pre class="x-cite x-pre" id="bib-' + p.id + '">' + esc(bibtex(p)) + '</pre>' : '') +
        '<p class="x-small" style="margin-bottom:.625rem"><a href="#' + p.id + '">Open the page of this publication</a>, with a link you can share</p>' +
        '<p class="x-small" style="margin-bottom:.625rem"><span class="x-muted">More by </span>' + surnames(p).map(function (n) { return '<button type="button" class="x-link" data-act="dbauthor" data-arg="' + esc(n) + '">' + esc(n) + '</button>'; }).join(', ') + '</p>' +
        (p.from ? '<p><a href="#' + p.from + '">Read the ERNC26 abstract this article grew out of</a></p>' : '') +
        (p.cited.length ? '<p class="x-small x-muted" style="margin-bottom:.375rem">Cited in the ERNC26 Book of Abstracts by</p><ul class="x-small" style="margin:0;padding-left:1.125rem;display:grid;gap:.25rem">' + p.cited.slice(0, 5).map(function (id) { return '<li><a href="#' + id + '">' + esc(ABS[id].title) + '</a></li>'; }).join('') + (p.cited.length > 5 ? '<li class="x-muted">and ' + (p.cited.length - 5) + ' more</li>' : '') + '</ul>' : '') + '</div>';
    }
    return '<li class="x-item" id="row-' + p.id + '"><div class="x-item-row"><button type="button" class="x-item-main" data-act="dbopen" data-arg="' + p.id + '" aria-expanded="' + open + '"' + (open ? ' aria-controls="detail-' + p.id + '"' : '') + '>' +
      '<span class="x-item-meta">' + TYPE[p.type] + ' · ' + p.y + (isNew ? ' · added ' + fmtDate(p.added) : '') + '</span><span class="x-item-title">' + hl(p.t, q) + '</span><span class="x-item-by">' + hl(p.au, q) + ' · ' + esc(p.v) + '</span>' +
      '<span class="x-item-tags">' + tags + '</span></button><span class="x-item-side">' + ic('down') + '</span></div>' + detail + '</li>';
  }
  function dbRows(all, shown) {
    return '<ul class="x-list">' + shown.map(function (r) {
      if (r.p) return pubItem(r.p, S.dbView === 'lists' ? '' : S.dbQ);
      var a = r.a;
      return '<li class="x-item"><div class="x-item-row"><a class="x-item-main" href="#' + a.id + '"><span class="x-item-meta">ERNC abstract · ERNC26 · 2026</span><span class="x-item-title">' + hl(a.title, S.dbQ) + '</span><span class="x-item-by">' + hl(a.aus, S.dbQ) + '</span>' + (a.video === 'yes' ? '<span class="x-item-tags">' + tag(PLAY + 'Video and transcript') + '</span>' : '') + '</a><span class="x-item-side">' + ic('arrow') + '</span></div></li>';
    }).join('') + '</ul>' + (all.length > shown.length ? '<div style="text-align:center"><button type="button" class="btn btn-outline" data-act="dbmore">Show ' + Math.min(30, all.length - shown.length) + ' more</button></div>' : '');
  }
  function dbListView() {
    if (!S.dbList) {
      return '<p class="x-muted x-lead" style="margin:0">Reading lists collect the publications on one subject. Your own list holds what you saved.</p><div class="quick-actions-grid">' +
        LISTS.map(function (l) { return '<button type="button" class="action-card x-cardbtn focus-ring" data-act="dblist" data-arg="' + l.id + '"><span class="action-icon">' + ic('book') + '</span><h3>' + esc(l.name) + '</h3><p>' + esc(l.note) + '</p><span class="action-link">' + l.ids.length + ' publications ' + ic('arrow', 'x-ic-sm') + '</span></button>'; }).join('') +
        '<a href="#saved" class="action-card focus-ring"><span class="action-icon">' + ic('save') + '</span><h3>My saved publications</h3><p>What you saved with “Save”. Kept in your saved library, next to your saved talks.</p><span class="action-link">' + myCount() + ' saved ' + ic('arrow', 'x-ic-sm') + '</span></a></div>' +
        demo('<p>The three lists are example selections. On the platform the Database Lead and members build them.</p>');
    }
    var all = dbAll(), list = LISTS.filter(function (x) { return x.id === S.dbList; })[0];
    return '<div><button type="button" class="x-link" data-act="dblist" data-arg="">' + ic('arrow', 'x-ic-sm x-back') + ' All reading lists</button></div>' +
      '<div class="x-card x-row x-between" style="padding:1rem 1.25rem"><div><h2 style="font-size:1.25rem;margin:0 0 .25rem">' + (list ? esc(list.name) : 'My list') + '</h2><p class="x-small x-muted" style="margin:0">' +
      (list ? esc(list.note) + ' ' + all.length + ' publications.' : (all.length ? all.length + ' saved. Only you see this list.' : 'Nothing saved yet. Open a publication in the search and choose “Add to my list”.')) + '</p></div>' +
      (all.length ? '<button type="button" class="btn btn-outline x-btn-sm" data-act="copylist">Copy the list as references</button>' : '') + '</div>' +
      (all.length ? dbRows(all, all) : '');
  }
  function dbResults() {
    if (S.dbView === 'lists') return dbListView();
    var all = dbAll(), shown = all.slice(0, S.dbLimit), total = D.pubs.length + D.abstracts.length;
    var line = '<p class="x-filterline" aria-live="polite"><span>' + (dbActive() ? all.length + ' of ' + total + ' entries' : 'All ' + total + ' entries') + (S.dbAuthor ? ' by ' + esc(S.dbAuthor) : '') + '</span>' +
      (dbActive() ? '<span class="x-row"><button type="button" class="x-link" data-act="savesearch">Save this search' + '</button><button type="button" class="x-link" data-act="dbclear">Clear search and filters</button></span>' : '') + '</p>';
    if (!all.length) return line + '<div class="x-empty">Nothing found. Try another word or remove a filter.</div>';
    return line + dbRows(all, shown);
  }
  function searchLabel(x) {
    var yl = { recent: '2024 to 2026', y20: '2020 to 2023', y10: '2010 to 2019', early: 'before 2010' }, tl = { article: 'Journal articles', book: 'Books and chapters', other: 'Reports and papers', abstract: 'ERNC abstracts' };
    return [x.q ? '“' + x.q + '”' : '', tl[x.type] || '', x.field, yl[x.years] || '', x.oa ? 'open access' : '', x.author ? 'by ' + x.author : ''].filter(Boolean).join(' · ') || 'Everything';
  }
  function pubView(id) {
    var p = PUB[id], mine = !!S.myList[id], inLists = LISTS.filter(function (l) { return l.ids.indexOf(id) >= 0; });
    return liveBar() + '<div class="container-ern content-wrap x-abstract">' + crumbs([['Database', '#database'], [TYPE[p.type]]]) +
      '<div class="x-cols"><article><p class="x-small x-muted" style="margin-bottom:.5rem">' + TYPE[p.type] + ' · ' + p.y + ' · ' + esc(p.v) + '</p><h1 tabindex="-1">' + esc(p.t) + '</h1>' +
      '<p class="x-muted x-lead">' + esc(p.au) + '</p><div class="x-item-tags" style="margin-bottom:1.25rem">' + tag(esc(p.field), 1) + (p.oa ? tag('Open access', 1) : '') + tag('English', 1) + (p.from ? tag('First presented at ERNC26') : '') + '</div>' +
      (p.sum ? '<p>' + esc(p.sum) + '</p>' : '') +
      '<h3 style="font-size:1.0625rem;margin:1.5rem 0 .5rem">Cite this publication</h3><p class="x-cite" id="cite-' + id + '">' + esc(apa(p)) + '</p>' +
      '<div class="x-row" style="margin:.75rem 0"><button type="button" class="btn btn-primary x-btn-sm" data-act="copypub" data-arg="' + id + '">Copy citation</button><button type="button" class="btn btn-outline x-btn-sm" data-act="copybib" data-arg="' + id + '" aria-expanded="' + (S.dbBib === id) + '">BibTeX</button>' +
      (p.doi ? ext('https://doi.org/' + p.doi, 'Open at the publisher', 'btn btn-outline x-btn-sm') : '') + '</div>' + (S.dbBib === id ? '<pre class="x-cite x-pre">' + esc(bibtex(p)) + '</pre>' : '') +
      (p.from ? '<p><a href="#' + p.from + '">Read the ERNC26 abstract this article grew out of</a></p>' : '') +
      (p.cited.length ? '<h3 style="font-size:1.0625rem;margin:1.5rem 0 .5rem">Cited in the ERNC26 Book of Abstracts</h3><ul class="x-botlist">' + p.cited.map(function (c) { return '<li><a href="#' + c + '">' + esc(ABS[c].title) + '</a></li>'; }).join('') + '</ul>' : '') + '</article>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>Keep and share</h3><div class="x-stack" style="gap:.625rem;align-items:flex-start"><button type="button" class="btn ' + (mine ? 'btn-outline' : 'btn-primary') + ' x-btn-sm" data-act="mylist" data-arg="' + id + '" aria-pressed="' + mine + '">' + ic('save') + (mine ? 'Saved in my library' : 'Save to my library') + '</button>' +
      '<button type="button" class="btn btn-outline x-btn-sm" data-act="copylink" data-arg="database/' + id.slice(2) + '">Copy the link to this page</button></div></div>' +
      '<div class="x-card"><h3>More like this</h3><p class="x-small" style="margin-bottom:.5rem"><span class="x-muted">By </span>' + surnames(p).map(function (n) { return '<button type="button" class="x-link" data-act="dbauthor" data-arg="' + esc(n) + '">' + esc(n) + '</button>'; }).join(', ') + '</p>' +
      (inLists.length ? '<p class="x-small" style="margin:0"><span class="x-muted">In the reading list </span>' + inLists.map(function (l) { return '<a href="#database" data-act="dblist" data-arg="' + l.id + '">' + esc(l.name) + '</a>'; }).join(', ') + '</p>' : '') + '</div>' +
      '<div class="x-card"><h3>About this entry' + mk() + '</h3><dl class="x-facts x-small" style="margin-bottom:.75rem">' + [['Type', TYPE[p.type] + ', published'], ['Details from', p.doi ? 'The publisher, through Crossref' : 'Entered by hand'], ['Summary', p.sum ? 'Written by the ERN database team' : 'None'], ['Added', p.added ? fmtDate(p.added) : 'With the first import'], ['Last checked', 'November 2026']].map(function (d) { return '<div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>' + d[0] + '</dt><dd>' + d[1] + '</dd></div>'; }).join('') + '</dl>' +
      (S.report === id ? '<form data-form="repsend"><label class="x-field" for="rep-in" style="margin-bottom:.5rem"><span>What is wrong?</span><textarea class="x-input" id="rep-in" rows="3" placeholder="For example a wrong year, a broken link, a missing author."></textarea></label><div class="x-row"><button class="btn btn-primary x-btn-sm" type="submit">Send the note</button><button type="button" class="x-link" data-act="report" data-arg="' + id + '">Cancel</button></div></form>' : '<button type="button" class="x-link" data-act="report" data-arg="' + id + '">Report a problem with this entry</button>') +
      '<p class="x-small x-muted" style="margin:.75rem 0 0">In this mockup the summaries were written for the mockup, and the dates are examples.</p></div></aside></div></div>';
  }
  function sel(id, label, opts, cur) {
    return '<label class="x-select" for="' + id + '"><span>' + label + '</span><select id="' + id + '" data-change="' + id + '">' + opts.map(function (o) { return '<option value="' + esc(o[0]) + '"' + (o[0] === cur ? ' selected' : '') + '>' + esc(o[1]) + '</option>'; }).join('') + '</select></label>';
  }
  function dbControls() {
    var types = [['all', 'Everything'], ['article', 'Journal articles'], ['book', 'Books and chapters'], ['other', 'Reports and papers'], ['abstract', 'ERNC abstracts']];
    var fields = [['', 'All topics']].concat(Object.keys(FIELDS).sort().map(function (f) { return [f, f + ' (' + FIELDS[f] + ')']; })), n = dbFilterN();
    return '<div class="x-chips" role="group" aria-label="Type of entry">' + types.map(function (t) { return chip(t[1], 'dbtype', t[0], S.dbType === t[0]); }).join('') + '</div>' +
      '<div class="x-filters x-between"><button type="button" class="btn btn-outline x-btn-sm" data-act="dbfilters" aria-expanded="' + S.dbFilters + '" aria-controls="db-panel">' + (S.dbFilters ? 'Hide filters' : 'More filters') + (n ? ' (' + n + ' on)' : '') + '</button>' +
      sel('db-sort', 'Sort by', [['new', S.dbQ ? 'Best match' : 'Newest first'], ['old', 'Oldest first'], ['cited', 'Most cited at ERNC26']], S.dbSort) + '</div>' +
      (S.dbFilters ? '<div class="x-card x-filters" id="db-panel" style="padding:1rem 1.25rem">' + sel('db-field', 'Topic', fields, S.dbField) +
        sel('db-years', 'Years', [['', 'All years'], ['recent', '2024 to 2026'], ['y20', '2020 to 2023'], ['y10', '2010 to 2019'], ['early', 'Before 2010']], S.dbYears) +
        sel('db-lang', 'Language', [['', 'All languages'], ['en', 'English (' + D.pubs.length + ')']], S.dbLang) +
        '<label class="x-select" for="db-author"><span>Author</span><input id="db-author" type="search" data-input="dbauthor" value="' + esc(S.dbAuthor) + '" placeholder="Surname" autocomplete="off"></label>' +
        chip('Open access only', 'dboa', '', S.dbOA) + (S.user ? chip('New since 1 October', 'dbnew', '', S.dbNew, ic('lock')) : '') + '<p class="x-small x-muted" style="margin:0;flex-basis:100%">Every entry of this sample is in English. The platform keeps work in other languages and lets you filter by it.</p></div>' : '');
  }
  function database() {
    var search = S.dbView !== 'lists';
    return liveBar() + pageHero({ title: 'Esports Research Database' + mk(), text: 'Journal articles, books and reports on esports, together with every ERNC abstract.' }) +
      '<div class="container-ern content-wrap x-stack">' +
      tabsRow('dbview', [['search', 'Search'], ['lists', 'Reading lists', LISTS.length + 1]], search ? 'search' : 'lists', 'Database') +
      (search ? '<div class="x-search">' + ic('search') + '<label class="x-vh" for="db-q">Search the database</label><input id="db-q" type="search" data-input="dbq" value="' + esc(S.dbQ) + '" placeholder="Search by title, author, journal or topic" autocomplete="off"></div>' +
      '<div id="db-controls" class="x-stack" style="gap:.875rem">' + dbControls() + '</div>' : '') +
      '<div id="db-results" class="x-stack" style="gap:.875rem" role="tabpanel">' + dbResults() + '</div>' +
      (search ? '<p class="x-small x-muted" style="margin:0">A publication opens in place with its summary and citation. An ERNC abstract opens on its own page with the talk. Is a publication missing? <button type="button" class="x-link" data-act="toast" data-arg="Not built in this mockup: a short form to suggest a publication would open here.">Suggest it</button>' + mk('idea') + '</p>' +
      '<details class="x-details x-card" style="padding:1rem 1.25rem"><summary>What is in the database, and how current it is' + mk() + '</summary><dl class="x-facts x-small" style="margin-top:.75rem"><div><dt>Holds</dt><dd>' + D.pubs.length + ' publications and ' + D.abstracts.length + ' conference abstracts. An abstract is not a published article, and every entry says which it is.</dd></div><div><dt>Comes from</dt><dd>New articles are found through Crossref, OpenAlex and PubMed and checked by the Database Lead before they appear. Members can suggest entries.</dd></div><div><dt>Coverage</dt><dd>Esports research in English from 2006 on. It is a selection, not a complete record of the field.</dd></div><div><dt>Last updated</dt><dd>10 November 2026</dd></div><div><dt>Found a mistake?</dt><dd>Open the page of the entry and choose “Report a problem with this entry”.</dd></div></dl></details>' +
      demo('<p>This mockup holds ' + D.pubs.length + ' real publications and the ' + D.abstracts.length + ' abstracts of ERNC26. The platform would start with the entries of the current database, including work in other languages.</p>' + ext('https://papers.esportsresearch.net', 'papers.esportsresearch.net')) : '') + '</div>';
  }
