
  /* ---------- assistant ---------- */
  var STOP = {}; 'a an and are about at be by can did do does esports esport ernc ernc26 find for from give has have how i in is it know me new of on or our please show talk talks talked tell that the there this to us was we were what which who with you articles article papers paper research presented presenting any some'.split(' ').forEach(function (w) { STOP[w] = 1; });
  var SCRIPTS = [
    { q: 'mental health', test: /mental|burnout|well-?being/, text: ['Recent work treats mental health in esports as more than a matter of individual symptoms. A network study of 1,025 players from over 60 countries found that preoccupation with the game sits downstream of other psychosocial strains, with mental fatigue connecting mood and sleep (Van Ruysevelt et al., 2026).', 'At ERNC26, Tom Brock argued that stress and burnout arise from the social relations of competitive gaming and need a sociological account.'],
      ids: function () { return ['p-van-ruysevelt-2026', aid('Esports and Mental Health'), 'p-poulus-2026', 'p-kegelaers-2025', aid('The Role of Social Connection')]; } },
    { q: 'integrity', test: /integrity|doping|match.?fixing|fixing/, text: ['Three ERNC26 talks dealt with integrity: Riedl and Caneppele on integrity measures at the 2025 IESF European Esports Championship, Reyes, Ressat and Souvignet with a scoping review that classifies e-doping risks, and Valentine and colleagues on how educational interventions shape attitudes to doping.'],
      ids: function () { return [aid('From Policy to Practice'), aid('Classifying E-Doping'), aid('Performance and Perception'), 'p-abarbanel-2019']; } },
    { q: 'exercise', test: /exercise|physical activity|training/, text: ['Two studies from 2026 looked at this. Wachholz and colleagues followed 18 competitive players for 30 days: 47.7% of the planned exercise sessions were completed, and lack of time was the most common barrier. Thillier and colleagues tested interval and cognitive-motor training with professional League of Legends and Valorant players during the season.'],
      ids: function () { return ['p-wachholz-2026', 'p-thillier-2026', aid('Daily Adherence'), aid('Relationship Between Esports Performance')]; } },
    { q: 'university', test: /universit|collegiate|college/, text: ['Several ERNC26 contributions dealt with university and collegiate esports, among them the panel on worldwide university esports competition structures.'],
      ids: function () { return [aid('Bridging Esports Futures'), aid('Bridging Collegiate Values'), aid('When the Roster Empties'), aid('The Collegiate-to-Professional'), aid('From Esport to Educated')]; } }
  ];
  function hitHtml(id) {
    var a = ABS[id], p = PUB[id];
    if (a) return '<button type="button" class="x-hit" data-act="askgo" data-arg="' + id + '"><small>ERNC26 talk' + (a.video === 'yes' ? ' · with video' : '') + '</small>' + esc(a.title) + '</button>';
    if (p) return '<button type="button" class="x-hit" data-act="askgo" data-arg="' + id + '"><small>' + TYPE[p.type] + ' · ' + p.y + '</small>' + esc(p.t) + '</button>';
    return '';
  }
  function answer(q) {
    var f = fold(q);
    var cover = 'This is what the sources below report. I add no conclusions of my own. The database is a selection, so other work may exist.';
    if (/\b(cause|causes|caused|prove|proves|proof|harmful|bad for|good for|addict)/.test(f)) {
      var hard = toks(q).filter(function (t) { return !STOP[t] && t.length > 3 && !/^(cause|causes|caused|prove|proves|proof|harmful|good|addict.*)$/.test(t); }), hits = [];
      D.pubs.concat(D.abstracts).forEach(function (it) { var sc = 0; hard.forEach(function (t) { if (it._h.indexOf(t) >= 0) sc += it._t.indexOf(t) >= 0 ? 3 : 1; }); if (sc) hits.push({ id: it.id, s: sc }); });
      hits.sort(function (x, y) { return y.s - x.s; });
      return { text: ['The collection cannot settle that. A question about cause needs studies that follow players over time, and I cannot tell from titles and summaries whether a study does.', hits.length ? 'What I can show: the entries on the topic. Read them as reports of what was observed, not as proof of a cause.' : 'I also found no entry on the topic itself.'], ids: hits.slice(0, 4).map(function (r) { return r.id; }), alts: hits.length ? [['Open all ' + hits.length + ' results in the database', 'askdb', hard.join(' ')]] : alts, note: hits.length ? 'Limited evidence' : '' };
    }
    for (var i = 0; i < SCRIPTS.length; i++) if (SCRIPTS[i].test.test(f)) return { text: SCRIPTS[i].text.concat([cover]), ids: SCRIPTS[i].ids().filter(Boolean), alts: [['Open the search results in the database', 'askdb', SCRIPTS[i].q]] };
    var alts = [['Search the database instead', 'askdb', q], ['Mental health in esports', 'asksugg', 'What do we know about mental health in esports?'], ['Talks on integrity', 'asksugg', 'Which talks at ERNC26 were about integrity?']];
    if (/\b(who will|will win|predict|forecast|weather|price|cost|salary|should i|best team|tomorrow|next year)\b/.test(f)) return { text: ['I cannot answer that. I only know what is in the ERN database, the ERNC abstracts and the recorded talks. I do not predict results, give advice or know current news.', 'You could try one of these instead.'], ids: [], alts: alts };
    var ts = toks(q).filter(function (t) { return !STOP[t] && t.length > 1; });
    if (!ts.length) return { text: ['Tell me a topic, a game, a method or a name, and I look it up in the database and the conference abstracts.'], ids: [] };
    var found = [];
    D.pubs.concat(D.abstracts).forEach(function (it) {
      var s = 0; ts.forEach(function (t) { if (it._h.indexOf(t) >= 0) s += it._t.indexOf(t) >= 0 ? 3 : (it._k.indexOf(t) >= 0 ? 2 : 1); });
      if (s) found.push({ id: it.id, s: s });
    });
    found.sort(function (x, y) { return y.s - x.s; });
    if (!found.length) return { text: ['I found nothing on “' + ts.join(' ') + '” in the database, the abstracts or the talks. I can only answer from these sources, so I will not guess.', 'You could try one of these instead.'], ids: [], alts: alts };
    return { text: ['I found ' + found.length + ' entr' + (found.length === 1 ? 'y' : 'ies') + ' for “' + ts.join(' ') + '”. These match best. I matched your words against titles, authors, keywords and summaries, I did not read the full texts.'], ids: found.slice(0, 5).map(function (r) { return r.id; }), alts: [['Open all ' + found.length + ' results in the database', 'askdb', ts.join(' ')]] };
  }
  function askRender() {
    var log = document.getElementById('ask-log'), av = '<span class="x-msg-av">' + OWL + '</span>';
    var first = '<div class="x-msg-row">' + av + '<div class="x-msg"><p>Hello, I am Ernie. I search the database, the conference abstracts and the talks for you. Ask a question or pick one of the examples below.</p></div></div>';
    log.innerHTML = first + S.askLog.map(function (m) {
      if (m.me) return '<div class="x-msg x-me"><p>' + esc(m.q) + '</p></div>';
      return '<div class="x-msg-row">' + av + '<div class="x-msg">' + m.text.map(function (t) { return '<p>' + esc(t) + '</p>'; }).join('') + (m.ids.length ? '<div class="x-hits-label">Sources</div><div class="x-hits">' + m.ids.map(hitHtml).join('') + '</div>' : '') + (m.note ? '<p class="x-small" style="margin:.5rem 0 0">' + tag(m.note, 1) + '</p>' : '') + (m.alts ? '<div class="x-hits">' + m.alts.map(function (x) { return '<button type="button" class="x-hit" data-act="' + x[1] + '" data-arg="' + esc(x[2]) + '">' + esc(x[0]) + '</button>'; }).join('') + '</div>' : '') + '</div></div>';
    }).join('');
    var mine = log.querySelectorAll('.x-me'), last = mine[mine.length - 1];
    log.scrollTop = last ? last.offsetTop - log.offsetTop - 8 : 0;
  }
  function ask(q) {
    q = String(q || '').trim(); if (!q) return;
    S.askLog.push({ me: true, q: q }); S.askLog.push(answer(q)); askRender();
  }
  function askOpen(open) {
    document.getElementById('ask').hidden = !open; document.getElementById('ask-btn').hidden = open;
    if (open) { askRender(); document.getElementById('ask-in').focus(); } else document.getElementById('ask-btn').focus();
  }
