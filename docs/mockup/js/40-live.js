
  /* ---------- live ---------- */
  function chatLog() { return S.chat.map(function (c) { return '<div><b>' + esc(c.u) + (c.mod ? ' (moderator)' : '') + '</b>' + esc(c.m) + '</div>'; }).join(''); }
  function live() {
    var now = D.liveDay[0], TW = 'https://www.twitch.tv/esportsresearchnetwork';
    var player = S.live
      ? '<div class="x-video" style="margin-bottom:1rem"><img src="img/hall.jpg" alt="The Main Hall during a session at ERNC26"><div class="x-video-top"><span class="x-livetag"><span class="x-dot"></span>Live</span><span>twitch.tv/esportsresearchnetwork</span></div></div>' +
        '<h2 style="font-size:1.5rem;margin-bottom:.5rem">' + esc(now.t) + '</h2><p class="x-muted" style="margin-bottom:1rem">' + esc(now.who) + ' · Session 5 · Main Hall</p>' +
        '<div class="x-row"><a href="#' + now.abs + '" class="btn btn-primary x-btn-sm">Read the abstract</a>' + ext(TW, 'Open on Twitch', 'btn btn-outline x-btn-sm') + '</div>'
      : '<div class="x-video" style="margin-bottom:1rem"><img src="img/hall.jpg" alt=""><div class="x-video-over"><strong style="font-size:1.25rem">The channel is offline</strong><span class="x-video-cap">Next stream: ERN Lecture Series, Wednesday 8 September 2027, 16:00 (Oslo time), an example date. When ERN goes live, the stream starts here and a banner appears on every page.</span>' + ext(TW, 'Open the channel on Twitch', 'btn btn-outline x-btn-sm') + '</div></div>';
    var side = S.live
      ? '<div class="x-chat"><div class="x-chat-head">Stream chat</div><div class="x-chat-log" id="chat-log" aria-live="polite">' + chatLog() + '</div><form class="x-chat-form" data-form="chat"><label class="x-vh" for="chat-in">Write a message</label><input id="chat-in" class="x-input" type="text" placeholder="Write a message" autocomplete="off"><button class="btn btn-primary" type="submit" aria-label="Send the message"><span style="display:flex;transform:rotate(90deg)">' + ic('send') + '</span></button></form></div>'
      : '<div class="x-card"><h3>Recordings</h3><p class="x-small x-muted">After a stream, each talk is cut and placed next to its abstract.</p><div style="display:grid;gap:.75rem">' +
        [['Keynote 1', "Japan's Esports Industry: Its Journey So Far, Present Challenges, and the Road Ahead"], ['Keynote 2', 'Game Heritage is Infrastructure: Esports & the Digital Society'], ['Keynote 3', 'From Grassroots to the Global Stage: Keeping Community at the Heart of Esports']].map(function (k) {
          return '<a class="x-small" style="font-weight:500" ' + extAttr('https://www.youtube.com/channel/UCy2Jz5v9oUTD7Y7glRbgBZA') + '><span class="x-muted" style="display:block;font-weight:400">ERNC26 · ' + k[0] + '</span>' + esc(k[1]) + EXTI + '</a>';
        }).join('') + '</div><p style="margin-top:1rem"><a href="#ernc26" data-act="confvideos" class="x-small">All ERNC26 talks with their abstracts</a></p></div>';
    var prog = '<div class="x-card"><h3>Programme</h3><p class="x-small x-muted">Example: ERNC26, Thursday, 17 September 2026, Main Hall</p><ul class="x-prog">' + D.liveDay.map(function (p, i) {
      return '<li><time' + (S.live && i === 0 ? ' class="x-now"' : '') + '>' + p.time + '</time><div><span style="font-weight:500">' + esc(p.t) + '</span>' + (S.live && i === 0 ? ' ' + tag('Now') : '') + '<span class="x-muted x-small" style="display:block">' + esc(p.who) + '</span></div></li>';
    }).join('') + '</ul></div>';
    return liveBar() + pageHero({ title: 'ERN Live' + mk(), text: 'Our Twitch channel on the site, with the programme next to it.' }) +
      '<div class="container-ern content-wrap x-stack">' +
      demo('<p>Show this page as it looks when the channel is</p><div class="x-chips" role="group" aria-label="State of the stream">' + chip('offline', 'setlive', '0', !S.live) + chip('live', 'setlive', '1', S.live) + '</div>') +
      '<div class="x-cols x-wide-side"><div>' + player + '</div><div class="x-stack" style="gap:1rem">' + side + '</div></div>' + prog +
      '<div class="x-card"><h3>Good to know</h3><dl class="x-facts x-small"><div><dt>Watching</dt><dd>Works on this page without any account.</dd></div><div><dt>Chat</dt><dd>Writing needs a free Twitch account. Reading does not.</dd></div><div><dt>Afterwards</dt><dd>The recording appears on the page of the lecture or the talk.</dd></div></dl></div>' +
      '<p class="x-small x-muted" style="margin:0">The preview uses day 3 of ERNC26 as its example. On the platform the player and the chat come from Twitch.</p></div>';
  }

  /* ---------- podcast, as on the current site ---------- */
  function podcast() {
    var C = D.podcast;
    return liveBar() + pageHero({ title: esc(C.title), text: 'Conversations with researchers on gaming and esports.' }) +
      '<div class="container-ern content-wrap x-stack" style="gap:2.5rem"><div class="x-cols"><div class="x-prose" style="font-size:1rem">' + paras(C.text) +
      '<p>To take part, write to the host at ' + esc(C.contact) + ' or reach out on the ' + ext(D.involved.discord, 'ERN Discord') + '.</p></div>' +
      '<aside class="x-card"><dl class="x-facts x-small" style="border-top:0">' + C.people.map(function (p) { return '<div style="grid-template-columns:5rem minmax(0,1fr)"><dt>' + esc(p[0]) + '</dt><dd><strong style="font-weight:600">' + esc(p[1]) + '</strong><span class="x-muted" style="display:block">' + esc(p[2]) + '</span></dd></div>'; }).join('') + '</dl></aside></div>' +
      '<div><div class="x-row x-between" style="margin-bottom:1.25rem"><h2 style="margin:0;font-size:1.5rem">Episodes</h2>' + ext(C.playlist, 'All episodes on YouTube', 'x-small') + '</div><div class="x-eps">' + C.episodes.map(function (e) {
        return '<a class="x-ep focus-ring" ' + extAttr('https://www.youtube.com/watch?v=' + e[2]) + '><div class="x-ep-art"><b>Episode ' + e[0] + '</b><span>' + PLAY + '</span></div><div class="x-ep-body"><strong>' + esc(e[1]) + '</strong><span>Watch on YouTube' + EXTI + '</span></div></a>';
      }).join('') + '</div></div></div>';
  }
