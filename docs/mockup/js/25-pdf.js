
  /* ---------- a Book of Abstracts as a PDF on the page ---------- */
  var PV = { src: '', loading: '', doc: null, n: 0, cur: 1, zoom: 0, text: [], indexed: 0, q: '', gen: 0, obs: null, ratio: 1.414, tick: false };
  var ZOOMS = [1, 1.25, 1.5, 2];
  function pdfHtml(c) {
    var same = PV.src === c.book;
    return '<div class="x-pdf" id="pdf" data-src="' + c.book + '" data-url="' + esc(c.pdf) + '">' +
      '<div class="x-pdf-bar">' +
      '<div class="x-pdf-group"><button type="button" class="x-pdf-btn" data-act="pdfstep" data-arg="-1" aria-label="Previous page">' + ic('up') + '</button>' +
      '<form class="x-pdf-pageform" data-form="pdfgo"><label for="pdf-page">Page</label><input id="pdf-page" class="x-input" type="number" inputmode="numeric" min="1" max="' + c.pages + '" value="' + (same ? PV.cur : 1) + '"><span>of ' + c.pages + '</span></form>' +
      '<button type="button" class="x-pdf-btn" data-act="pdfstep" data-arg="1" aria-label="Next page">' + ic('down') + '</button></div>' +
      '<div class="x-search x-pdf-search">' + ic('search') + '<label class="x-vh" for="pdf-q">Search in this book</label><input id="pdf-q" type="search" data-input="pdfq" value="' + esc(same ? PV.q : '') + '" placeholder="Search in this book" autocomplete="off"></div>' +
      '<div class="x-pdf-group"><button type="button" class="x-pdf-btn" data-act="pdfzoom" data-arg="-1" aria-label="Zoom out">' + ic('minus') + '</button><span class="x-pdf-zoom" id="pdf-zoom" aria-live="polite">100%</span>' +
      '<button type="button" class="x-pdf-btn" data-act="pdfzoom" data-arg="1" aria-label="Zoom in">' + ic('plus') + '</button></div></div>' +
      '<div class="x-pdf-hits" id="pdf-hits" hidden></div>' +
      '<div class="x-pdf-pages" id="pdf-pages" tabindex="0" role="region" aria-label="' + esc(c.name) + ' Book of Abstracts, ' + c.pages + ' pages. Scroll to read.">' +
      '<p class="x-pdf-state" id="pdf-state"><span class="x-spin" aria-hidden="true"></span>Loading the Book of Abstracts</p></div></div>';
  }
  function pdfFail() {
    var st = document.getElementById('pdf-pages'), box = document.getElementById('pdf'); if (!st || !box) return;
    st.innerHTML = '<p class="x-pdf-state">The book could not be shown here.&nbsp;' + ext(box.getAttribute('data-url'), 'Open the PDF in a new tab') + '</p>';
  }
  function pdfStop() { PV.gen++; if (PV.obs) { PV.obs.disconnect(); PV.obs = null; } }
  function pdfMount() {
    var box = document.getElementById('pdf');
    pdfStop();
    if (!box) return;
    var src = box.getAttribute('data-src');
    if (PV.src === src && PV.doc) { pdfBuild(PV.gen); return; }
    if (PV.loading === src) return;                       /* already on its way: it builds the pages when it arrives */
    if (PV.doc) { try { PV.doc.destroy(); } catch (e) { /* already gone */ } }
    PV.src = src; PV.loading = src; PV.doc = null; PV.n = 0; PV.cur = 1; PV.zoom = 0; PV.q = ''; PV.text = []; PV.indexed = 0;
    var tries = 0, own = false;
    function shown() { var b = document.getElementById('pdf'); return b && b.getAttribute('data-src') === src; }
    function failed() { if (PV.loading === src) PV.loading = ''; if (PV.src === src && !PV.doc) { PV.src = ''; if (shown()) pdfFail(); } }
    (function start() {
      if (!window.pdfjsLib) {
        /* the library comes from the CDN; if it has not arrived after two seconds, our own copy is loaded instead */
        if (++tries === 10 && !own) { own = true; var sc = document.createElement('script'); sc.src = 'lib/pdf.min.js'; document.head.appendChild(sc); }
        if (tries > 75) failed(); else setTimeout(start, 200);
        return;
      }
      window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'lib/pdf.worker.min.js';
      fetch(src).then(function (r) { if (!r.ok) throw new Error('not found'); return r.arrayBuffer(); })
        .then(function (buf) { return window.pdfjsLib.getDocument({ data: buf, isEvalSupported: false }).promise; })
        .then(function (doc) {
          if (PV.loading === src) PV.loading = '';
          if (PV.src !== src) { doc.destroy(); return; }
          PV.doc = doc; PV.n = doc.numPages;
          return doc.getPage(1).then(function (page) {
            var v = page.getViewport({ scale: 1 }); PV.ratio = v.height / v.width;
            pdfIndex(doc);
            if (shown()) pdfBuild(PV.gen);
          });
        })
        .catch(failed);
    })();
  }
  function pdfBuild(gen) {
    var pages = document.getElementById('pdf-pages'); if (!pages) return;
    var html = '';
    for (var i = 1; i <= PV.n; i++) html += '<div class="x-pdf-page" id="pdf-p' + i + '" data-page="' + i + '" style="aspect-ratio:1 / ' + PV.ratio.toFixed(4) + '"><span class="x-pdf-no">' + i + '</span></div>';
    pages.innerHTML = '<div class="x-pdf-col" id="pdf-col" style="--z:' + ZOOMS[PV.zoom] + '">' + html + '</div>';
    document.getElementById('pdf-zoom').textContent = Math.round(ZOOMS[PV.zoom] * 100) + '%';
    PV.obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) pdfDraw(Number(en.target.getAttribute('data-page')), gen); });
    }, { root: pages, rootMargin: '700px 0px' });
    Array.prototype.forEach.call(pages.querySelectorAll('.x-pdf-page'), function (el) { PV.obs.observe(el); });
    pages.addEventListener('scroll', pdfOnScroll, { passive: true });
    if (PV.cur > 1) pdfJump(PV.cur);
    pdfHits();
  }
  function pdfDraw(n, gen) {
    var slot = document.getElementById('pdf-p' + n), z = ZOOMS[PV.zoom];
    if (!slot || gen !== PV.gen || !PV.doc || slot._z === z || slot._busy) return;
    slot._busy = true;
    PV.doc.getPage(n).then(function (page) {
      if (gen !== PV.gen || !slot.isConnected) return;
      var cssW = slot.clientWidth || 600, base = page.getViewport({ scale: 1 });
      var dpr = Math.min(window.devicePixelRatio || 1, 2); if (cssW * dpr > 1800) dpr = 1800 / cssW;
      var vp = page.getViewport({ scale: cssW * dpr / base.width });
      var canvas = document.createElement('canvas'); canvas.width = Math.floor(vp.width); canvas.height = Math.floor(vp.height);
      canvas.setAttribute('role', 'img'); canvas.setAttribute('aria-label', 'Page ' + n);
      return page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise.then(function () {
        if (gen !== PV.gen || !slot.isConnected) return;
        slot.innerHTML = ''; slot.appendChild(canvas); slot._z = z;
        /* keep only the pages around the reader in memory */
        Array.prototype.forEach.call(document.querySelectorAll('.x-pdf-page'), function (s) {
          var k = Number(s.getAttribute('data-page'));
          if (s._z && Math.abs(k - PV.cur) > 6 && Math.abs(k - n) > 6) { s.innerHTML = '<span class="x-pdf-no">' + k + '</span>'; s._z = 0; }
        });
      });
    }).catch(function () { /* a page that fails stays a numbered blank */ }).then(function () { slot._busy = false; });
  }
  function pdfOnScroll() {
    if (PV.tick) return; PV.tick = true;
    requestAnimationFrame(function () {
      PV.tick = false;
      var pages = document.getElementById('pdf-pages'), first = document.getElementById('pdf-p1'); if (!pages || !first) return;
      var second = document.getElementById('pdf-p2'), step = second ? second.offsetTop - first.offsetTop : first.offsetHeight + 12;
      var cur = Math.min(PV.n, Math.max(1, Math.floor((pages.scrollTop + pages.clientHeight * 0.3) / step) + 1));
      if (cur !== PV.cur) { PV.cur = cur; var i = document.getElementById('pdf-page'); if (i && document.activeElement !== i) i.value = cur; }
    });
  }
  function pdfJump(n) {
    var pages = document.getElementById('pdf-pages'), slot = document.getElementById('pdf-p' + n); if (!pages || !slot) return;
    PV.cur = n; pages.scrollTop = slot.offsetTop - 8;
    var i = document.getElementById('pdf-page'); if (i) i.value = n;
  }
  function pdfIndex(doc) {
    var i = 0;
    (function next() {
      if (PV.doc !== doc) return;
      if (i >= doc.numPages) { PV.indexed = doc.numPages; pdfHits(); return; }
      i++;
      doc.getPage(i).then(function (p) { return p.getTextContent(); }).then(function (tc) {
        PV.text[i] = tc.items.map(function (it) { return it.str; }).join(' ').replace(/\s+/g, ' ');
      }, function () { PV.text[i] = ''; }).then(function () { PV.indexed = i; if (PV.q && i % 25 === 0) pdfHits(); next(); });
    })();
  }
  function pdfHits() {
    var box = document.getElementById('pdf-hits'); if (!box) return;
    var ts = toks(PV.q).filter(function (t) { return t.length > 1; });
    if (!ts.length) { box.hidden = true; box.innerHTML = ''; return; }
    var hits = [];
    for (var i = 1; i <= PV.indexed; i++) {
      var raw = PV.text[i] || '', f = fold(raw);
      if (ts.every(function (t) { return f.indexOf(t) >= 0; })) {
        var at = Math.max(0, f.indexOf(ts[0]) - 60);
        hits.push({ n: i, s: (at ? '… ' : '') + raw.slice(at, at + 170) + ' …' });
      }
    }
    var done = PV.n && PV.indexed >= PV.n;
    box.hidden = false;
    box.innerHTML = '<p class="x-filterline" aria-live="polite"><span>' + (hits.length ? 'Found on ' + hits.length + ' page' + (hits.length === 1 ? '' : 's') + '.' : (done ? 'Not found in this book.' : '')) +
      (done ? '' : ' Still reading the book: page ' + PV.indexed + ' of ' + (PV.n || '…') + '.') + '</span><button type="button" class="x-link" data-act="pdfclear">Clear the search</button></p>' +
      (hits.length ? '<ul class="x-pdf-hitlist">' + hits.slice(0, 60).map(function (h) {
        return '<li><button type="button" class="x-pdf-hit" data-act="pdfgo" data-arg="' + h.n + '"><b>Page ' + h.n + '</b><span>' + hl(h.s, PV.q) + '</span></button></li>';
      }).join('') + (hits.length > 60 ? '<li class="x-small x-muted" style="padding:.5rem .75rem">and ' + (hits.length - 60) + ' more pages</li>' : '') + '</ul>' : '');
  }
