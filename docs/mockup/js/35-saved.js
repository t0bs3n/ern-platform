
  /* ---------- saved library: publications, talks and searches ---------- */
  function saved() {
    var pubs = Object.keys(S.myList).filter(function (k) { return S.myList[k] && PUB[k]; }), talks = Object.keys(S.saved).filter(function (k) { return S.saved[k] && ABS[k]; });
    var rm = function (act, id, what) { return '<button type="button" class="x-link x-rm" data-act="' + act + '" data-arg="' + id + '" aria-label="Remove ' + esc(what) + ' from my library">Remove</button>'; };
    var empty = function (t) { return '<div class="x-empty" style="padding:1.25rem">' + t + '</div>'; };
    return liveBar() + pageHero({ title: 'Saved library' + mk(), text: 'The publications, talks and searches you want to come back to.' }) +
      '<div class="container-ern content-wrap"><div class="x-cols"><div class="x-stack" style="gap:2.5rem">' +
      '<div class="x-card x-notice" role="status">' + ic(S.user ? 'check' : 'save') + '<div>' + (S.user ? '<strong>Kept in your account</strong><p class="x-small" style="margin:.125rem 0 0">You find the same library on every device where you sign in.</p>'
        : '<strong>Kept in this browser only</strong><p class="x-small" style="margin:.125rem 0 0">Without an account your library stays on this device. <a href="#members">Sign in</a> and it moves into your account.</p>') + '</div></div>' +
      '<section><div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">Publications (' + pubs.length + ')</h2>' + (pubs.length ? '<button type="button" class="btn btn-outline x-btn-sm" data-act="copysaved">Copy as references</button>' : '') + '</div>' +
      (pubs.length ? '<ul class="x-list">' + pubs.map(function (id) { var p = PUB[id]; return '<li class="x-item"><div class="x-item-row x-admrow"><a class="x-item-main x-plainlink" href="#' + id + '"><span class="x-item-meta">' + TYPE[p.type] + ' · ' + p.y + '</span><span class="x-item-title">' + esc(p.t) + '</span><span class="x-item-by">' + esc(p.au) + '</span></a>' + rm('mylist', id, p.t) + '</div></li>'; }).join('') + '</ul>'
        : empty('Nothing saved yet. Open a publication in the <a href="#database">database</a> and choose “Save”.')) + '</section>' +
      '<section><h2 class="x-h2">Talks and abstracts (' + talks.length + ')</h2>' +
      (talks.length ? '<ul class="x-list">' + talks.map(function (id) { var a = ABS[id]; return '<li class="x-item"><div class="x-item-row x-admrow"><a class="x-item-main x-plainlink" href="#' + id + '"><span class="x-item-meta">ERNC26' + (a.video === 'yes' ? ' · with video' : '') + '</span><span class="x-item-title">' + esc(a.title) + '</span><span class="x-item-by">' + esc(a.aus) + '</span></a>' + rm('save', id, a.title) + '</div></li>'; }).join('') + '</ul>'
        : empty('Nothing saved yet. Use the bookmark on a talk in the <a href="#ernc26" data-act="conftab" data-arg="book">ERNC26 abstracts</a>.')) + '</section>' +
      '<section><h2 class="x-h2">Saved searches (' + S.searches.length + ')</h2>' +
      (S.searches.length ? '<ul class="x-list">' + S.searches.map(function (x, n) {
        return '<li class="x-item"><div class="x-item-row x-admrow"><div class="x-item-main"><span class="x-item-meta">Search in the database</span><span class="x-item-title">' + esc(searchLabel(x)) + '</span><span class="x-item-by">' + (S.user ? 'You get an e-mail when new entries match.' : 'Sign in to get an e-mail when new entries match.') + '</span></div><div class="x-row">' +
          (S.user ? '<label class="x-vh" for="ss-' + n + '">How often</label><select id="ss-' + n + '" class="x-miniselect" data-change="ss-' + n + '">' + ['Every week', 'Every month', 'No e-mail'].map(function (o) { return '<option' + (o === x.freq ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select>' : '') +
          '<button type="button" class="btn btn-outline x-btn-sm" data-act="runsearch" data-arg="' + n + '">Run it</button><button type="button" class="x-link x-rm" data-act="rmsearch" data-arg="' + n + '">Remove</button></div></div></li>';
      }).join('') + '</ul>' : empty('No saved search yet. Search or filter the <a href="#database">database</a>, then choose “Save this search”.')) + '</section></div>' +
      '<aside class="x-stack" style="gap:1rem"><div class="x-card"><h3>My agenda</h3><p class="x-small x-muted">The talks you plan to attend at a conference are kept apart from this library.</p><p class="x-small" style="margin-bottom:.75rem"><strong>' + agendaCount() + '</strong> talks in your ERNC26 agenda</p><a href="#ernc26" data-act="conftab" data-arg="prog" class="btn btn-outline x-btn-sm">Open the programme</a></div>' +
      demo('<p>Nothing is stored for real. A reload empties the library.</p>') + '</aside></div></div>';
  }
