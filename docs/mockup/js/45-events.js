
  /* ---------- events calendar: conferences, lectures, deadlines, association ---------- */
  var EVENTS = [
    ['2026-11-15', '', 'Call for nominations to the board closes', 'Association', 'ERN members', '#members'],
    ['2026-12-09', '', 'Motions for the General Assembly are due', 'Association', 'ERN members', '#members'],
    ['2026-12-16', '13:00', 'General Assembly 2026', 'Association', 'On Zoom, 14:00 in Oslo', '#members'],
    ['2027-02-28', '', 'Autumn lecture series: proposals are due', 'Deadline', 'Call for autumn 2027, an example date', '#propose'],
    ['2027-01-04', '', 'ERNC27: abstract deadline', 'Deadline', 'Presentations, posters, panels and workshops', '#ernc27'],
    ['2027-03-15', '', 'ERNC27: decisions sent to authors', 'Deadline', 'To the corresponding author', '#ernc27'],
    ['2027-04-03', '', 'ERNC27: registration opens', 'Deadline', 'At NC State University', '#ernc27'],
    ['2027-09-01', '', 'ERNC27: registration deadline for presenters', 'Deadline', 'At least one author per contribution', '#ernc27']
  ].concat(COMING.map(function (c, n) { return [c[3], n < 4 ? '14:00' : '15:00', 'Lecture ' + (n + 1) + ': ' + c[1], 'Lecture', 'Live on ERN Live, 16:00 in Oslo', '#lectures', n]; })).concat([
    ['2027-11-08', '', 'ERNC27: pre-conference workshops', 'Conference', 'NC State University, Raleigh, USA', '#ernc27'],
    ['2027-11-09', '', 'ERNC27: Think & Do (9 to 11 November)', 'Conference', 'NC State University, Raleigh, USA', '#ernc27']
  ]).sort(function (x, y) { return x[0] < y[0] ? -1 : 1; });
  function evWhen(e) {
    if (!e[1]) return new Date(e[0] + 'T12:00:00Z').toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
    var d = new Date(e[0] + 'T' + e[1] + ':00Z');
    return d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) + ', ' + d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) + ' your time';
  }
  function evTitle(e) { return e[6] != null ? 'Lecture ' + (e[6] + 1) + ': ' + progOf(e[6]).title : e[2]; }
  function evRow(e) {
    return '<li class="x-item"><div class="x-item-row x-admrow"><a class="x-item-main x-plainlink" href="' + (e[6] != null ? '#lec-' + (e[6] + 1) : e[5]) + '"><span class="x-item-meta">' + evWhen(e) + '</span><span class="x-item-title">' + esc(evTitle(e)) + '</span><span class="x-item-by">' + (e[6] != null && progOf(e[6]).who ? esc(progOf(e[6]).who) + ' · ' : '') + esc(e[4]) + '</span></a><span class="x-row">' + tag(e[3], 1) +
      '<button type="button" class="btn btn-outline x-btn-sm" data-act="toast" data-arg="Not built in this mockup: a calendar entry for “' + esc(evTitle(e)) + '” would be saved, in your own time zone.">Add to calendar</button></span></div></li>';
  }
  function events() {
    var types = ['all', 'Conference', 'Lecture', 'Deadline', 'Association'], list = EVENTS.filter(function (e) { return S.evType === 'all' || e[3] === S.evType; });
    var zone = ''; try { zone = Intl.DateTimeFormat().resolvedOptions().timeZone || ''; } catch (err) { zone = ''; }
    return liveBar() + pageHero({ title: 'Events and deadlines' + mk(), text: 'Conferences, lectures, deadlines and the dates of the association in one calendar.' }) +
      '<div class="container-ern content-wrap x-stack">' +
      '<div class="x-chips" role="group" aria-label="Kind of event">' + types.map(function (t) { return chip(t === 'all' ? 'Everything' : t === 'Association' ? 'Association' : t + 's', 'evtype', t, S.evType === t); }).join('') + '</div>' +
      '<p class="x-small x-muted" style="margin:0">Times are shown in your own time zone' + (zone ? ' (' + esc(zone.replace(/_/g, ' ')) + ')' : '') + '. Dates without a time are deadlines that end with the day.</p>' +
      '<ul class="x-list">' + list.map(evRow).join('') + '</ul>' +
      demo('<p>The ERNC27 dates are from the Call for Abstracts. The General Assembly and the lecture dates are the examples used elsewhere in this mockup.</p>') + '</div>';
  }
