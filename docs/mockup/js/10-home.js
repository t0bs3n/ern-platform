
  /* ---------- what membership opens: shown locked to visitors, filled for members ---------- */
  function memLink(tab, label) { return '<a class="action-link" href="#members" data-act="memgo" data-arg="' + tab + '">' + label + ' ' + ic('arrow', 'x-ic-sm') + '</a>'; }
  function memberBand() {
    var head = '<div class="section-header"><h2>For members' + mk() + '</h2><p>' + (S.user ? 'New in the journals and in the industry, picked up for you every day.' : 'Members see what is new in the journals and in the industry, every day, in one place.') + '</p></div>';
    if (S.user) {
      var arts = NEWPUBS.slice(0, 3).map(function (p) {
        return '<li><a href="#database" data-act="openpub" data-arg="' + p.id + '"><span class="x-item-meta">Added ' + fmtDate(p.added) + ' · ' + esc(p.v.split(',')[0]) + '</span><span class="x-feed-title">' + esc(p.t) + '</span></a></li>';
      });
      var press = [D.press[7], D.press[1], D.press[9]].map(function (n) {
        return '<li><a ' + extAttr(n.url) + '><span class="x-item-meta">' + esc(n.src) + ' · ' + esc(n.when) + '</span><span class="x-feed-title">' + esc(n.t) + EXTI + '</span></a></li>';
      });
      return '<section class="section-spacing bg-secondary-light"><div class="container-ern">' + head + '<div class="x-three x-two-up">' +
        feedCol('New journal articles' + lockTag(), NEWPUBS.length + ' since 1 October 2026', arts, memLink('articles', 'All new articles')) +
        feedCol('Esports press' + lockTag(), 'From The Esports Radar and The Esports Advocate', press, memLink('press', 'All headlines')) + '</div></div></section>';
    }
    var cards = [
      ['doc', 'New journal articles', NEWPUBS.length + ' articles since 1 October 2026. The list fills itself as articles are published.', 'articles'],
      ['globe', 'Esports press', 'The headlines of The Esports Radar and The Esports Advocate, collected every day.', 'press'],
      ['users', 'Community', 'The member feed, the member directory and the notices of the board.', 'feed']
    ].map(function (x) {
      return '<a href="#members" data-act="memgo" data-arg="' + x[3] + '" class="action-card focus-ring"><div class="action-icon">' + ic(x[0]) + '</div><h3>' + x[1] + '</h3><p>' + x[2] + '</p><div class="x-lockline">' + lockTag() + '</div></a>';
    }).join('');
    return '<section class="section-spacing bg-secondary-light"><div class="container-ern">' + head + '<div class="quick-actions-grid">' + cards + '</div>' +
      '<div class="hero-buttons" style="animation:none;margin-top:2rem"><a href="#join" class="btn btn-primary btn-lg">Become a member</a><a href="#members" class="btn btn-outline btn-lg">Sign in</a></div></div></section>';
  }

  /* ---------- home ---------- */
  function feedCol(title, sub, items, more) { return '<div class="x-feed"><h3>' + title + '</h3><p>' + sub + '</p><ul>' + items.join('') + '</ul>' + more + '</div>'; }
  function home() {
    var H = D.home, c = D.ernc27;
    var explore = [
      ['book', 'Database', 'Journal articles, books and reports on esports, together with every ERNC abstract.', '#database', 'Search the database'],
      ['video', 'Abstracts and talks', 'Each Book of Abstracts on the page, with every abstract next to its recorded talk.', '#ernc26', 'Browse ERNC26'],
      ['live', 'ERN Live', 'The Twitch stream on our own site, with the programme and the abstracts beside it.', '#live', 'Open ERN Live'],
      ['cap', 'Teaching library', 'Lectures prepared for the classroom, with readings, questions and a teacher guide.', '#lectures', 'Browse the units']
    ].map(function (x) {
      return '<a href="' + x[3] + '" class="action-card focus-ring"><div class="action-icon">' + ic(x[0]) + '</div><h3>' + x[1] + mk() + '</h3><p>' + x[2] + '</p><span class="action-link">' + x[4] + ' ' + ic('arrow', 'x-ic-sm') + '</span></a>';
    }).join('');
    var ern = D.news.slice(0, 3).map(function (n) {
      return '<a class="news-card focus-ring" ' + extAttr(n.url) + '><div class="news-date">' + ic('cal') + esc(n.d) + '</div><h3>' + esc(n.t) + '</h3><p>' + esc(n.x) + '</p><span class="x-newsfoot">Read the post' + EXTI + '</span></a>';
    }).join('');
    return liveBar() +
      '<section class="hero-section"><div class="container-ern"><div style="max-width:64rem">' +
      '<h1 tabindex="-1">' + esc(H.h1a) + ' <span class="text-primary">' + esc(H.h1b) + '</span></h1><p class="x-herolead">' + esc(H.lead) + '</p>' +
      '<div class="hero-buttons"><a href="#join" class="btn btn-primary btn-lg">Join ERN</a><a href="#conferences" class="btn btn-outline btn-lg">ERNC27 Details</a></div>' +
      '<form class="x-search x-hero-search" data-form="homesearch" role="search">' + ic('search') + '<label class="x-vh" for="home-q">Search the database, the abstracts and the talks</label><input id="home-q" type="search" placeholder="Search research, abstracts and talks" autocomplete="off"><button class="btn btn-primary x-btn-sm" type="submit">Search</button></form>' +
      (S.mark ? '<div style="margin-top:.625rem">' + mk() + '</div>' : '') +
      '</div></div></section>' +

      '<section class="x-routes"><div class="container-ern"><h2 class="x-vh">Where to start</h2><div class="x-three">' +
      [['search', 'Find research', 'Publications, conference abstracts and recorded talks in one search.', [['Search the database', '#database', ''], ['ERNC26 abstracts and talks', '#ernc26', ' data-act="conftab" data-arg="book"'], ['Reading lists', '#database', ' data-act="dblist" data-arg=""']]],
       ['cap', 'Teach with ERN', 'Lectures prepared for the classroom, free to use.', [['Teaching units for one class', '#lectures', ' data-act="gosec" data-arg="lectures|sec-units"'], ['The autumn lecture series', '#lectures', ' data-act="gosec" data-arg="lectures|sec-autumn"'], ['Run it as a course', '#course', '']]],
       ['users', 'Join and contribute', 'Become a member, present your work, help the network.', [['Become a member', '#join', ''], ['Propose a lecture', '#propose', ''], ['Ways to get involved', '#involved', '']]]].map(function (r) {
        return '<div class="x-card x-route"><div class="action-icon">' + ic(r[0]) + '</div><h3>' + r[1] + mk() + '</h3><p>' + r[2] + '</p><ul>' + r[3].map(function (l) { return '<li><a href="' + l[1] + '"' + l[2] + '>' + l[0] + ' ' + ic('arrow', 'x-ic-sm') + '</a></li>'; }).join('') + '</ul></div>';
      }).join('') + '</div></div></section>' +
      '<section class="section-spacing bg-background"><div class="container-ern"><div class="conference-card"><div style="display:flex;flex-direction:column;gap:2rem"><div style="flex:1">' +
      '<div class="conference-badge"><span></span> Featured Event</div>' +
      '<h2>' + esc(c.short + ': ' + c.theme) + '</h2>' +
      '<div class="conference-info">' + info('cal', c.dates) + info('clock', c.mode) + info('globe', c.place) + '</div>' +
      '<div class="conference-tags" style="margin-bottom:0"><a href="#ernc27" data-act="c27tab" data-arg="call" class="badge-circle">Call for Abstracts</a><a href="#ernc27" data-act="c27tab" data-arg="submit" class="badge-circle">Submit</a><a href="#conduct" class="badge-circle">Code of Conduct</a></div>' +
      '</div><div style="flex-shrink:0"><a href="#conferences" class="btn btn-primary btn-lg">Learn More</a></div></div></div></div></section>' +

      '<section class="x-sponsorband"><div class="container-ern"><div class="x-row x-between" style="margin-bottom:.75rem"><h2 style="font-size:1.125rem;margin:0">Coming up' + mk() + '</h2><a class="x-small" href="#events">All events and deadlines</a></div><ul class="x-upnext">' + EVENTS.filter(function (e) { return e[0] >= '2026-11-12'; }).slice(0, 3).map(function (e) { return '<li><a href="' + (e[6] != null ? '#lec-' + (e[6] + 1) : e[5]) + '"><span class="x-item-meta">' + evWhen(e) + '</span><strong>' + esc(evTitle(e)) + '</strong></a></li>'; }).join('') + '</ul></div></section>' +
      '<section class="section-spacing bg-background"><div class="container-ern"><div class="section-header-flex"><div><h2>News &amp; Updates</h2><p style="color:hsl(var(--muted-foreground));margin:0">' + esc(H.newsLead) + '</p></div>' +
      '<a href="#news" class="btn btn-outline">View all ' + ic('arrow', 'x-ic-sm') + '</a></div><div class="news-grid">' + ern + '</div></div></section>' +


      '<section class="section-spacing bg-background"><div class="container-ern"><div class="goals-grid"><div><h2>What ERN Does</h2><p style="color:hsl(var(--muted-foreground));margin-bottom:2rem;max-width:42rem">' + esc(H.goalsLead) + '</p>' + goalsList() + '</div>' +
      '<div class="goals-image"><div class="x-photo"><img src="img/group.jpg" alt="The participants of ERNC26 in Tsukuba, September 2026" loading="lazy"></div></div></div></div></section>' +

      '<section class="section-spacing bg-secondary-light"><div class="container-ern"><div class="section-header"><h2>Get Involved</h2><p>Multiple ways to participate in the ERN community</p></div><div class="quick-actions-grid">' + involvedCards() + '</div></div></section>';
  }

  /* ---------- news: the posts of the network; press and new articles live in the member area ---------- */
  function news() {
    var teaser = S.user
      ? '<div class="x-card x-row x-between"><div><h3 style="margin-bottom:.25rem">More in your member area</h3><p class="x-small x-muted" style="margin:0">New journal articles and the esports press, picked up every day.</p></div><div class="x-row"><a href="#members" data-act="memgo" data-arg="articles" class="btn btn-primary x-btn-sm">New articles</a><a href="#members" data-act="memgo" data-arg="press" class="btn btn-outline x-btn-sm">Esports press</a></div></div>'
      : '<div class="x-card x-row x-between"><div><h3 style="margin-bottom:.25rem;display:flex;flex-wrap:wrap;align-items:center;gap:.5rem">More for members ' + lockTag() + mk() + '</h3><p class="x-small x-muted" style="margin:0">Members also get new journal articles and the esports press, picked up every day.</p></div><div class="x-row"><a href="#join" class="btn btn-primary x-btn-sm">Become a member</a><a href="#members" data-act="memgo" data-arg="articles" class="btn btn-outline x-btn-sm">Sign in</a></div></div>';
    return liveBar() + pageHero({ title: 'News', text: esc(D.home.newsLead) }) +
      '<div class="container-ern content-wrap x-stack" style="gap:2rem">' + demo('<p>The posts are the ones on the current website. In this mockup a post opens there.</p>') +
      '<div class="news-grid">' + D.news.map(function (n) {
        return '<a class="news-card focus-ring" ' + extAttr(n.url) + '><div class="news-date">' + ic('cal') + esc(n.d) + '</div><h3>' + esc(n.t) + '</h3><p>' + esc(n.x) + '</p><span class="x-newsfoot">Read the post' + EXTI + '</span></a>';
      }).join('') + '</div>' + teaser + '</div>';
  }
