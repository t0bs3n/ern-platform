
  /* ---------- admin area ---------- */
  var APPS = [{ id: 'ap1', name: 'Noor Haddad', inst: 'Example University', country: 'Jordan', status: 'PhD student', when: '9 November 2026' },
    { id: 'ap2', name: 'Tomás Herrera', inst: 'Example Institute of Technology', country: 'Chile', status: 'Researcher with a PhD', when: '8 November 2026' },
    { id: 'ap3', name: 'Linh Pham', inst: 'Example Esports Club', country: 'Vietnam', status: 'Practitioner in industry', when: '6 November 2026' }];
  var EXSUBS = [{ no: 'ERNC27-038', title: 'Scholastic leagues as a pathway into collegiate programmes', who: 'Rafael Monteiro', strand: 'Esports Competition', format: 'Presentation, in person', rev: 2, st: 'In review' },
    { no: 'ERNC27-039', title: 'Eye tracking in first-person shooters: a methods note', who: 'Ji-ho Baek', strand: 'Esports Research', format: 'Poster', rev: 2, st: 'In review' },
    { no: 'ERNC27-040', title: 'Grassroots clubs and participation in mid-sized cities', who: 'Aiko Tanabe', strand: 'Esports Community', format: 'Presentation, online', rev: 1, st: 'Received' },
    { no: 'ERNC27-041', title: 'Sponsorship fit in university esports', who: 'Lukas Brandt', strand: 'Esports Competition', format: 'Presentation, in person', rev: 0, st: 'Received' }];
  var STATI = ['Received', 'In review', 'Reviews complete'];
  var RESULTS = [['Acceptance', 'Accepted'], ['Acceptance subject to revisions', 'Revisions requested'], ['Rejection', 'Rejected']];
  var REVIEWS = [['Reviewer 1', 'Acceptance', 'A clear question and a sound method. The link to the conference theme could be stated in the introduction.'],
    ['Reviewer 2', 'Acceptance subject to revisions', 'The results section reports anticipated results only. Please say so in the text and shorten the references.']];
  var ROLES = [['Member', 'Sees the member area'], ['Reviewer', 'Reviews the abstracts assigned to them'], ['Final editor', 'Gives the final result on every submission. One person holds this role.'], ['Conference team', 'Manages conferences, submissions, programme and sponsors'], ['Database Lead', 'Checks new articles and edits the reading lists'], ['Editor', 'Writes news, lectures and notices'], ['Admin', 'Everything, including the rights of other members']];
  var ROLE0 = { 'Amara Okafor': ['Member', 'Database Lead', 'Reviewer'], 'Lukas Brandt': ['Member', 'Conference team', 'Final editor'], 'Aiko Tanabe': ['Member', 'Reviewer'], 'Maja Lindqvist': ['Member', 'Admin'] };
  function rolesOf(name) { return S.adm.roles[name] || ROLE0[name] || ['Member']; }
  function reviewers() { return D.people.map(function (p) { return p.name; }).filter(function (n) { return rolesOf(n).indexOf('Reviewer') >= 0; }); }
  function roleBox(name) {
    var has = rolesOf(name);
    return '<form class="x-card x-subdetail" id="adm-rolebox" data-form="admroles" data-name="' + esc(name) + '"><div class="x-row x-between" style="margin-bottom:.75rem"><h3 style="margin:0">Rights of ' + esc(name) + '</h3><button type="button" class="x-link" data-act="admrole" data-arg="">Close</button></div>' +
      '<fieldset class="x-fieldset"><legend class="x-vh">Roles</legend>' + ROLES.map(function (r, n) {
        return '<label class="x-check" for="role-' + n + '"><input type="checkbox" id="role-' + n + '" value="' + r[0] + '"' + (has.indexOf(r[0]) >= 0 ? ' checked' : '') + (n === 0 ? ' disabled' : '') + '><span><strong>' + r[0] + '</strong><span class="x-small x-muted" style="display:block">' + r[1] + '</span></span></label>';
      }).join('') + '</fieldset><div class="x-row" style="margin-top:1rem"><button class="btn btn-primary x-btn-sm" type="submit">Save the rights</button></div></form>';
  }
  function assignBox(x) {
    var got = S.adm.assign[x.no] || [], pool = reviewers().filter(function (n) { return n !== x.who; });
    if (got.length) return got.map(function (n) { return '<div class="x-review"><strong>' + esc(n) + '</strong><span class="x-small x-muted"> has been asked. The review is open.</span></div>'; }).join('') + '<button type="button" class="x-link" data-act="admunassign" data-arg="' + x.no + '">Change the reviewers</button>';
    return '<form data-form="admassign" data-no="' + x.no + '"><p class="x-small x-muted">No review yet. Choose two members who hold the role “Reviewer”.</p><fieldset class="x-fieldset"><legend class="x-vh">Reviewers</legend>' +
      pool.map(function (n, k) { return '<label class="x-check" for="rv-' + k + '"><input type="checkbox" id="rv-' + k + '" value="' + esc(n) + '"><span>' + esc(n) + '</span></label>'; }).join('') + '</fieldset>' +
      '<div class="x-row" style="margin-top:.75rem"><button class="btn btn-outline x-btn-sm" type="submit">Assign the reviewers</button><a class="x-small" href="#admin" data-act="admtab" data-arg="members">Make a member a reviewer</a></div></form>';
  }
  var CONF0 = [['ERNC27', 'Raleigh, USA · 9 to 11 November 2027', 'Open for submissions'], ['ERNC26', 'Tsukuba, Japan · 15 to 17 September 2026', 'Past, with programme and Book of Abstracts']];
  function confBox() {
    var extra = S.adm.confs;
    return '<div><div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">Conferences</h2><button type="button" class="btn btn-primary x-btn-sm" data-act="admadd" data-arg="conf" aria-expanded="' + (S.adm.add === 'conf') + '">Add a conference</button></div>' +
      (S.adm.add === 'conf' ? '<form class="x-card x-subdetail x-form" data-form="admconf" novalidate style="margin:0 0 1rem"><h3 style="margin:0">New conference</h3><div class="x-two">' + fld('nc-name', 'Short name', { req: 1, ph: 'ERNC28' }) + fld('nc-theme', 'Theme') + '</div>' +
        '<div class="x-two">' + fld('nc-place', 'Place', { req: 1, ph: 'University, city, country' }) + fld('nc-dates', 'Dates', { req: 1, ph: '10 to 12 November 2028' }) + '</div>' +
        '<div class="x-two">' + fld('nc-mode', 'Format', { req: 1, opts: ['In person', 'Online', 'Hybrid'] }) + fld('nc-due', 'Abstract deadline', { ph: '8 January 2028' }) + '</div>' +
        '<p class="x-small x-muted" style="margin:0">The conference starts as a draft. Call for Abstracts, submission form, programme and sponsors are added step by step and published when ready.</p>' +
        '<div class="x-row"><button class="btn btn-primary x-btn-sm" type="submit">Create as a draft</button><button type="button" class="btn btn-outline x-btn-sm" data-act="admadd" data-arg="">Cancel</button></div></form>' : '') +
      '<ul class="x-admlist x-card" style="padding:.25rem 1.25rem">' + extra.map(function (c) { return [c.name, c.place + ' · ' + c.dates, 'Draft, not public']; }).concat(CONF0).map(function (c) {
        return '<li><span><strong>' + esc(c[0]) + '</strong> <small class="x-muted">' + esc(c[1]) + '</small></span><span class="x-row" style="flex-wrap:nowrap">' + tag(esc(c[2]), c[2].indexOf('Open') !== 0) + '<button type="button" class="x-link" data-act="toast" data-arg="' + NOTBUILT + 'the pages of this conference would be edited here: call, programme, sponsors.">Edit</button></span></li>';
      }).join('') + '<li><span class="x-muted">ERNC25, ERNC24, ERNC22 and ERNC21 <small>Past</small></span></li></ul></div>';
  }
  function subDetail(x) {
    var d = S.adm.dec[x.no];
    return '<section class="x-card x-subdetail" id="adm-detail" aria-label="Submission ' + x.no + '"><div class="x-row x-between" style="margin-bottom:.75rem"><div><span class="x-item-meta">' + x.no + ' · ' + esc(x.strand) + ' · ' + esc(x.format) + '</span><h3 style="margin:.125rem 0 0">' + esc(x.title) + '</h3><span class="x-small x-muted">' + esc(x.who) + '</span></div>' +
      '<button type="button" class="x-link" data-act="admsub" data-arg="">Close</button></div>' +
      '<div class="x-two" style="gap:1.5rem;align-items:start"><div><h4 class="x-h4" style="margin-top:0">1. Reviews</h4>' +
      (x.rev ? REVIEWS.slice(0, x.rev).map(function (r) { return '<div class="x-review"><strong>' + r[0] + '</strong><span class="x-small x-muted"> recommends: ' + r[1] + '</span><p class="x-small" style="margin:.25rem 0 0">' + r[2] + '</p></div>'; }).join('') : assignBox(x)) +
      '<p class="x-small x-muted" style="margin:.75rem 0 0">Each reviewer writes comments and a recommendation. The final editor, one person for all submissions, decides.</p></div>' +
      '<div><h4 class="x-h4" style="margin-top:0">2. Final decision by the editor</h4>' +
      (d ? '<p class="x-small" style="margin-bottom:.5rem"><strong>Decision: ' + esc(d.res) + '</strong> · ' + (d.sent ? 'sent on ' + d.sent : 'saved, not sent yet') + '</p>' +
           '<div class="x-mail"><p class="x-small x-muted" style="margin:0 0 .375rem">' + (d.sent ? 'What the author received' : 'Preview of the e-mail to the author') + '</p><p class="x-small" style="margin:0"><strong>To:</strong> ' + esc(x.who) + ', corresponding author<br><strong>Subject:</strong> ERNC27: decision on your abstract ' + x.no + '</p>' +
           '<p class="x-small" style="margin:.5rem 0 0">Dear ' + esc(x.who.split(' ')[0]) + ', the Scientific Committee has reviewed “' + esc(x.title) + '”. The decision is: <strong>' + esc(d.res) + '</strong>.' + (d.fb ? ' Feedback from the editor: ' + esc(d.fb) : '') + ' You find the details on your submission page.</p></div>' +
           (d.sent ? '<ul class="x-small x-botlist" style="margin-top:.75rem">' + (S.adm.hist[x.no] || []).map(function (h) { return '<li>' + esc(h) + '</li>'; }).join('') + '</ul>'
                   : '<div class="x-row" style="margin-top:.75rem"><button type="button" class="btn btn-primary x-btn-sm" data-act="admsend" data-arg="' + x.no + '">Send to the author</button><button type="button" class="x-link" data-act="admundo" data-arg="' + x.no + '">Change the decision</button></div>')
         : '<form data-form="admdec" data-no="' + x.no + '" novalidate class="x-stack" style="gap:.875rem">' +
           fld('adm-fb', 'Feedback for the authors', { area: 1, rows: 4, help: 'The authors receive this text with the decision. Notes of the reviewers stay internal unless you take them over.' }) +
           fld('adm-res', 'Final decision', { req: 1, opts: RESULTS.map(function (r) { return r[0]; }) }) +
           '<div class="x-row"><button class="btn btn-primary x-btn-sm" type="submit">Save the decision</button><button type="button" class="btn btn-outline x-btn-sm" data-act="admtake">Take over the reviewer comments</button></div><p class="x-small x-muted" style="margin:0">Saving does not send anything. You see the e-mail first.</p></form>') +
      '</div></div></section>';
  }
  function admSubs() { return EXSUBS.concat(S.subs.map(function (x) { return { no: x.no, title: x.title, who: S.me.name, strand: x.strand, format: x.format, rev: 0, st: 'Received' }; })); }
  function appsOpen() { return APPS.filter(function (a) { return !S.adm.apps[a.id]; }).length; }
  function queueOpen() { return NEWPUBS.slice(0, 4).filter(function (p) { return !S.adm.queue[p.id]; }).length; }
  function nb(label, what) { return '<button type="button" class="btn btn-outline x-btn-sm" data-act="toast" data-arg="' + NOTBUILT + what + '">' + label + '</button>'; }
  var WF = ['Proposed', 'Under review', 'Selected', 'Scheduled', 'Materials in preparation', 'Ready for live delivery', 'Recording ready', 'Teaching unit published'];
  var WFTODO = [['Confirm receipt to the speaker', 'Name an ERN owner'], ['Two members of the lecture team read the proposal', 'Check the fit with the theme and the level'], ['Send the decision with feedback', 'Agree a date and a time zone'], ['Put the date into the events calendar', 'Send the speaker template'],
    ['Slides received', 'Questions and reflection prompts written', 'Activity and worksheet drafted', 'Permissions for recording and slides signed'], ['Test the stream with the speaker', 'Announce the lecture to members and courses'], ['Recording checked', 'Chapters marked by time', 'Transcript corrected'], ['Teacher guide reviewed', 'Listed in the Teaching library']];
  var PROP0 = [{ id: 'ex1', title: 'Player contracts: what researchers should know', who: 'Priya Raman', level: 'Master', desc: 'How contracts shape careers, and what that means for studies with players.', when: 'October', step: 1 },
    { id: 'ex2', title: 'Streaming as a workplace', who: 'Elif Demir', level: 'Bachelor', desc: 'Labour, income and audience work of streamers.', when: 'September or November', step: 3 }];
  function PROPS() { return S.props.concat(PROP0); }
  function stepOf(x) { return S.adm.wf[x.id] == null ? x.step : S.adm.wf[x.id]; }
  var OUTS = ['Needs changes', 'Declined', 'Deferred to next autumn', 'Withdrawn', 'Cancelled'];
  function progOf(n) { var id = S.adm.prog[n], x = id && PROPS().filter(function (q) { return q.id === id; })[0]; return x ? { title: x.title, who: x.who } : { title: COMING[n][1], who: '' }; }
  function draftProg() { var m = {}; PROPS().forEach(function (x) { var sl = S.adm.slot[x.id]; if (sl != null && stepOf(x) >= 3 && !S.adm.out[x.id]) m[sl] = x.id; }); return m; }
  function progDiffers() { var d = draftProg(), p = S.adm.prog; for (var n = 0; n < COMING.length; n++) if ((d[n] || '') !== (p[n] || '')) return true; return false; }
  function freeSlot(id) { var d = draftProg(); for (var n = 0; n < COMING.length; n++) if (!d[n] || d[n] === id) return n; return null; }
  function propDetail(x) {
    var st = stepOf(x), out = S.adm.out[x.id], fb = S.adm.fb[x.id] || [], slot = S.adm.slot[x.id], d = draftProg();
    var outs = OUTS.filter(function (o) { return o === out || (st <= 1 ? o !== 'Cancelled' : st === 2 ? o === 'Withdrawn' || o === 'Deferred to next autumn' : o === 'Cancelled' || o === 'Withdrawn' || o === 'Deferred to next autumn'); });
    return '<section class="x-card x-subdetail" id="prop-detail" aria-label="Proposal"><div class="x-row x-between" style="margin-bottom:.75rem"><div><span class="x-item-meta">Proposal by ' + esc(x.who) + (x.ext ? ', not a member' : '') + ' · ' + esc(x.level) + (x.when ? ' · available: ' + esc(x.when) : '') + '</span><h3 style="margin:.125rem 0 0">' + esc(x.title) + '</h3></div><button type="button" class="x-link" data-act="admprop" data-arg="">Close</button></div>' +
      '<p class="x-small">' + esc(x.desc) + '</p>' +
      (out ? '<div class="x-notice" style="padding:0" role="status">' + ic(out === 'Needs changes' ? 'pen' : 'x') + '<div><strong>Outcome: ' + out + '</strong><p class="x-small" style="margin:.125rem 0 .5rem">What the speaker reads: ' + OUTMSG[out] + '</p><button type="button" class="x-link" data-act="wfout" data-arg="' + x.id + '|">Reopen the proposal</button></div></div>'
           : '<ol class="x-wf" aria-label="Progress">' + WF.map(function (w, n) { return '<li class="' + (n < st ? 'x-done' : n === st ? 'x-now' : '') + '"' + (n === st ? ' aria-current="step"' : '') + '><span>' + (n + 1) + '</span>' + w + '</li>'; }).join('') + '</ol>') +
      '<div class="x-two" style="gap:1.5rem;align-items:start;margin-top:1.25rem"><div>' +
      (out ? '' : '<h4 class="x-h4" style="margin-top:0">To do at this step</h4><ul class="x-checklist">' + WFTODO[st].map(function (t, n) { return '<li><label class="x-check" for="wf-' + n + '"><input type="checkbox" id="wf-' + n + '"><span>' + t + '</span></label></li>'; }).join('') + '</ul>' +
        (st >= 3 ? '<p class="x-small" style="margin:.75rem 0 0">' + sel('wf-slot', 'Date', COMING.map(function (c, n) { return [String(n), c[0].replace('Wednesday ', '') + (d[n] && d[n] !== x.id ? ' (taken)' : '')]; }), String(slot)) + '</p>' : '') +
        '<p class="x-small" style="margin:.75rem 0"><span class="x-muted">ERN owner: </span>Amara Okafor</p>' +
        '<div class="x-row">' + (st < WF.length - 1 ? '<button type="button" class="btn btn-primary x-btn-sm" data-act="wfnext" data-arg="' + x.id + '">Move to: ' + WF[st + 1] + '</button>' : tag(ic('check') + 'Published in the Teaching library')) + (st > 0 ? '<button type="button" class="x-link" data-act="wfback" data-arg="' + x.id + '">One step back</button>' : '') + '</div>' +
        '<p class="x-small" style="margin:1rem 0 .375rem;font-weight:600">Or another outcome</p><div class="x-chips">' + outs.map(function (o) { return chip(o, 'wfout', x.id + '|' + o, false); }).join('') + '</div><p class="x-small x-muted" style="margin:.5rem 0 0">Write the feedback first. The speaker sees the outcome and the feedback together.</p>') + '</div>' +
      '<div class="x-stack" style="gap:1rem"><label class="x-field" for="lec-int"><span>Internal notes</span><textarea class="x-input" id="lec-int" rows="3" placeholder="Only the lecture team sees this."></textarea><small>Never sent to the speaker.</small></label>' +
      '<form data-form="lecfb" data-id="' + x.id + '"><label class="x-field" for="lec-fb" style="margin-bottom:.625rem"><span>Feedback to the speaker</span><textarea class="x-input" id="lec-fb" rows="3"></textarea></label><button class="btn btn-outline x-btn-sm" type="submit">Send the feedback</button></form>' +
      (fb.length ? '<div><p class="x-small" style="font-weight:600;margin-bottom:.25rem">Sent to the speaker</p><ul class="x-small x-botlist" style="margin:0">' + fb.map(function (f) { return '<li><span class="x-muted">' + esc(f[0]) + ':</span> ' + esc(f[1]) + '</li>'; }).join('') + '</ul></div>' : '') + '</div></div></section>';
  }
  function unitEditor(a) {
    var st = S.adm.unit[a.id] || { ver: 0, hist: [] }, dr = st.draft, out = dr ? dr.out : unitOut(a), sl = dr ? dr.slides : slidesFree(a), rep = (dr && dr.files) || {}, rec = dr ? dr.rec : unitRec(a), ch = dr ? dr.ch : unitCh(a), url = dr ? dr.url : (st.url || 'https://www.youtube.com/watch?v=example');
    return '<form class="x-card x-subdetail" id="unit-editor" data-form="unitedit" data-id="' + a.id + '" novalidate><div class="x-row x-between" style="margin-bottom:.75rem"><div><span class="x-item-meta">Edit teaching unit · published version ' + unitVer(a) + (dr ? ' · draft for 1.' + (st.ver + 1) + ' saved ' + dr.at : '') + '</span><h3 style="margin:.125rem 0 0">' + esc(a.title) + '</h3></div><button type="button" class="x-link" data-act="unitopen" data-arg="">Close</button></div>' +
      '<div class="x-two" style="gap:1.5rem;align-items:start"><div class="x-stack" style="gap:1rem"><label class="x-field" for="ue-out"><span>Learning outcomes</span><textarea class="x-input" id="ue-out" rows="5">' + esc(out.join('\n')) + '</textarea><small>One per line. Shown on the unit page and in the teacher guide.</small></label>' +
      '<fieldset class="x-fieldset"><legend>Recording</legend><label class="x-field" for="ue-url"><span>Link to the recording</span><input class="x-input" id="ue-url" type="text" value="' + esc(url) + '" autocomplete="off"></label><label class="x-field" for="ue-rec"><span>Length in minutes</span><input class="x-input" id="ue-rec" type="number" min="1" max="180" value="' + rec + '" style="max-width:8rem"><small>The class plans and the unit page are built from this number.</small></label>' +
      '<label class="x-field" for="ue-ch"><span>Chapters <small>(optional)</small></span><textarea class="x-input" id="ue-ch" rows="4" placeholder="0:00 Introduction">' + esc(ch.map(function (c) { return c[0] + ' ' + c[1]; }).join('\n')) + '</textarea><small>One per line: a time and a title. They only help to jump. The recording is never cut.</small></label></fieldset>' +
      '<fieldset class="x-fieldset"><legend>Permissions of the speaker</legend><label class="x-check" for="ue-rec"><input type="checkbox" id="ue-rec" checked disabled><span>The recording may be shown and linked</span></label><label class="x-check" for="ue-slides"><input type="checkbox" id="ue-slides"' + (sl ? ' checked' : '') + '><span>Teachers may adapt the slides</span></label><p class="x-small x-muted" style="margin:0">Signed by the speaker on 2 November 2026 (example).</p></fieldset></div>' +
      '<div><p class="x-small" style="font-weight:600;margin-bottom:.25rem">Materials</p><ul class="x-admlist">' + FILES.map(function (f, n) { return '<li><span>' + f[0] + ' <small class="x-muted">' + (rep[n] ? 'New file in the draft, ' + rep[n] : 'As published') + '</small></span><button type="button" class="x-link" data-act="uefile" data-arg="' + a.id + '|' + n + '">' + (rep[n] ? 'Replace again' : 'Replace') + '</button></li>'; }).join('') + '</ul>' +
      (st.hist.length ? '<p class="x-small" style="font-weight:600;margin:1rem 0 .25rem">Versions</p><ul class="x-small x-botlist" style="margin:0">' + st.hist.map(function (h) { return '<li>' + esc(h) + '</li>'; }).join('') + '<li>1.0 published in November 2026</li></ul>' : '') + '</div></div>' +
      (S.adm.unitPrev ? '<div class="x-mail" style="margin-top:1rem"><p class="x-small x-muted" style="margin:0 0 .375rem">Preview: what teachers will see after publishing</p><p class="x-small" style="margin:0 0 .25rem"><strong>Students will be able to</strong></p><ul class="x-small x-botlist" style="margin:0 0 .5rem">' + out.map(function (o) { return '<li>' + esc(o) + '</li>'; }).join('') + '</ul><p class="x-small" style="margin:0 0 .25rem">Recording: ' + rec + ' minutes, ' + ch.length + ' chapters. The 60-minute plan becomes: ' + planFor('60', rec).rows.map(function (r) { return r[0] + ' ' + r[1].toLowerCase(); }).join(', ') + '.</p><p class="x-small" style="margin:0">Version 1.' + (st.ver + 1) + ' · Slides: ' + (sl ? 'teachers may adapt them' : 'changes need the consent of the speaker') + '</p></div>' : '') +
      '<div class="x-row" style="margin-top:1rem"><button class="btn btn-outline x-btn-sm" type="submit">Save the draft</button><button type="button" class="btn btn-outline x-btn-sm" data-act="unitprev" aria-pressed="' + !!S.adm.unitPrev + '">Preview</button><button type="button" class="btn btn-primary x-btn-sm" data-act="unitpub" data-arg="' + a.id + '"' + (dr ? '' : ' disabled') + '>Publish version 1.' + (st.ver + 1) + '</button>' + (dr ? '' : '<span class="x-small x-muted">Save a draft first.</span>') + '</div><p class="x-small x-muted" style="margin:.75rem 0 0">Publishing changes the unit page, the class plans and the package. Nobody is notified. Teachers see the new version number.</p></form>';
  }
  function teachingAdmin() {
    var v = S.admLec, props = PROPS(), body;
    if (v === 'programme') {
      var d = draftProg(), diff = progDiffers();
      body = '<div class="x-card x-notice" role="status">' + ic(diff ? 'pen' : 'check') + '<div><strong>' + (diff ? 'The draft differs from the public programme' : 'The public programme matches this plan') + '</strong><p class="x-small" style="margin:.125rem 0 ' + (diff ? '.5rem' : '0') + '">' + (diff ? 'Visitors still see the published version. Check the preview, then publish. Publishing changes the Teaching page, the lecture pages and the events calendar. The speakers of newly listed lectures get an e-mail, nobody else.' : 'What you see here is what visitors see on the Teaching page and in the events calendar.') + '</p>' +
        (diff ? '<div class="x-row"><button type="button" class="btn btn-primary x-btn-sm" data-act="progpub">Publish the programme</button><button type="button" class="btn btn-outline x-btn-sm" data-act="progprev" aria-pressed="' + !!S.adm.progPrev + '">Preview the public list</button></div>' : '<a class="x-small" href="#lectures" data-act="gosec" data-arg="lectures|sec-autumn">See it on the Teaching page</a>') + '</div></div>' +
        (S.adm.progPrev && diff ? '<div class="x-mail"><p class="x-small x-muted" style="margin:0 0 .375rem">Preview: the public list after publishing</p><ol class="x-small" style="margin:0;padding-left:1.25rem">' + COMING.map(function (c, n) { var x = d[n] && props.filter(function (q) { return q.id === d[n]; })[0]; return '<li>' + c[0].replace('Wednesday ', '') + ': ' + (x ? '<strong>' + esc(x.title) + '</strong>, ' + esc(x.who) : esc(c[1]) + ', speaker to be announced') + '</li>'; }).join('') + '</ol></div>' : '') +
        '<div class="x-table"><table class="x-plaintable" style="min-width:50rem !important"><thead><tr><th>Date</th><th>Lecture</th><th>Speaker</th><th>ERN owner</th><th>Step</th><th>Public</th></tr></thead><tbody>' +
        COMING.map(function (c, n) { var x = d[n] && props.filter(function (q) { return q.id === d[n]; })[0], pub = (S.adm.prog[n] || '') === (d[n] || ''); return '<tr><td>' + c[0].replace('Wednesday ', '') + '</td><td>' + (x ? '<strong>' + esc(x.title) + '</strong>' : '<span class="x-muted">Open slot: ' + esc(c[1]) + '</span>') + '</td><td>' + (x ? esc(x.who) : '<span class="x-muted">To be found</span>') + '</td><td>' + (x ? 'Amara Okafor' : '') + '</td><td>' + (x ? WF[stepOf(x)] : '') + '</td><td>' + (pub ? 'Yes' : '<strong>Draft only</strong>') + '</td></tr>'; }).join('') + '</tbody></table></div>' +
        '<p class="x-small x-muted" style="margin:0">A proposal enters the draft when it is scheduled. Its date is set in the proposal.</p>';
    } else if (v === 'library') {
      var open = LECT.filter(function (a) { return a.id === S.adm.unitOpen; })[0];
      body = '<div class="x-table"><table class="x-plaintable" style="min-width:50rem !important"><thead><tr><th>Teaching unit</th><th>Recording</th><th>Guide and handout</th><th>Permissions</th><th>Version</th><th>Status</th><th></th></tr></thead><tbody>' +
        LECT.map(function (a) { var u = unitOf(a), st = S.adm.unit[a.id]; return '<tr><td><strong>' + esc(a.title) + '</strong></td><td>Ready</td><td>' + (u.ready ? 'Ready' : 'In preparation') + '</td><td>' + (u.ready ? 'Signed' : 'Asked') + '</td><td>' + (u.ready ? unitVer(a) + (st && st.draft ? ', draft saved' : '') : 'Draft') + '</td><td>' + (u.ready ? 'Published' : 'Recording only') + '</td><td>' + (u.ready ? '<button type="button" class="btn btn-outline x-btn-sm" data-act="unitopen" data-arg="' + a.id + '" aria-expanded="' + (S.adm.unitOpen === a.id) + '">Edit</button>' : '') + '</td></tr>'; }).join('') +
        props.filter(function (x) { return stepOf(x) >= 4 && !S.adm.out[x.id]; }).map(function (x) { var done = stepOf(x) >= 7, sent = S.ws.sent[x.id]; return '<tr><td><strong>' + esc(x.title) + '</strong>' + (sent ? '<br>' + tag('Changes from the speaker to review, sent ' + esc(sent)) : '') + '</td><td>' + (stepOf(x) >= 6 ? 'Ready' : 'Not yet') + '</td><td>' + (done ? 'Ready' : 'In preparation') + '</td><td>' + (stepOf(x) >= 5 ? 'Signed' : 'Asked') + '</td><td>' + (done ? '1.0' : 'Draft') + '</td><td>' + (done ? 'Published' : WF[stepOf(x)]) + '</td><td>' + (sent ? '<button type="button" class="btn btn-outline x-btn-sm" data-act="wsaccept" data-arg="' + x.id + '">Accept the changes</button>' : '') + '</td></tr>'; }).join('') + '</tbody></table></div>' +
        (open ? unitEditor(open) : '<p class="x-small x-muted" style="margin:0">Speakers update their own material in “My lectures”. It goes public only after an editor has accepted it here.</p>');
    } else {
      body = '<ul class="x-list">' + props.map(function (x) {
        var out = S.adm.out[x.id];
        return '<li class="x-item"><div class="x-item-row x-admrow"><div class="x-item-main"><span class="x-item-meta">' + esc(x.who) + ' · ' + esc(x.level) + '</span><span class="x-item-title">' + esc(x.title) + '</span></div><div class="x-row">' + (out ? tag(out, 1) : tag('Step ' + (stepOf(x) + 1) + ' of 8: ' + WF[stepOf(x)], stepOf(x) < 7)) + '<button type="button" class="btn btn-outline x-btn-sm" data-act="admprop" data-arg="' + x.id + '" aria-expanded="' + (S.adm.lecOpen === x.id) + '">Open</button></div></div></li>';
      }).join('') + '</ul>' + (props.filter(function (x) { return x.id === S.adm.lecOpen; }).map(propDetail)[0] || '') +
        '<p class="x-small x-muted" style="margin:0">Also interested in presenting, without a proposal yet: ' + (S.me.present ? esc(S.me.name) + ', ' : '') + 'Rafael Monteiro, Aiko Tanabe (from their profiles).</p>';
    }
    return '<div class="x-chips" role="group" aria-label="View">' + [['proposals', 'Proposals (' + props.length + ')'], ['programme', 'Autumn programme' + (progDiffers() ? ' · draft to publish' : '')], ['library', 'Teaching library']].map(function (o) { return chip(o[1], 'admlec', o[0], v === o[0]); }).join('') + '</div>' + body +
      demo('<p>The two example proposals are invented. A proposal sent from the form on the Teaching page appears here as well.</p>');
  }
  function admBody() {
    var t = S.admTab;
    if (t === 'teaching') return teachingAdmin();
    if (t === 'members') {
      return '<div><h2 class="x-h2">Applications to confirm</h2><ul class="x-list">' + APPS.map(function (a) {
        var st = S.adm.apps[a.id];
        return '<li class="x-item"><div class="x-item-row x-admrow"><div class="x-item-main"><span class="x-item-meta">Applied on ' + a.when + '</span><span class="x-item-title">' + esc(a.name) + '</span><span class="x-item-by">' + esc(a.status) + ' · ' + esc(a.inst) + ' · ' + esc(a.country) + '</span></div><div class="x-row">' +
          (st ? tag(st === 'ok' ? ic('check') + 'Approved, welcome e-mail sent' : 'Declined', st !== 'ok') + '<button type="button" class="x-link" data-act="admapp" data-arg="' + a.id + '|">Undo</button>'
              : '<button type="button" class="btn btn-primary x-btn-sm" data-act="admapp" data-arg="' + a.id + '|ok">Approve</button><button type="button" class="btn btn-outline x-btn-sm" data-act="admapp" data-arg="' + a.id + '|no">Decline</button>') + '</div></div></li>';
      }).join('') + '</ul></div>' +
        '<div><div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">Member list</h2><div class="x-row">' + nb('Export the list', 'the member list would be exported, for example for the General Assembly.') + nb('Write to all members', 'an e-mail to all members would be written here.') + '</div></div>' +
        '<div class="x-table"><table class="x-plaintable"><thead><tr><th>Name</th><th>Status</th><th>Country</th><th>Role on the platform</th><th></th></tr></thead><tbody>' + D.people.map(function (p, n) {
          return '<tr><td>' + esc(p.name) + '</td><td>' + esc(p.role) + '</td><td>' + esc(p.country) + '</td><td>' + esc(rolesOf(p.name).join(', ')) + '</td><td><button type="button" class="x-link" data-act="admrole" data-arg="' + esc(p.name) + '" aria-expanded="' + (S.adm.edit === p.name) + '">Edit rights</button></td></tr>';
        }).join('') + '</tbody></table></div>' + (S.adm.edit ? roleBox(S.adm.edit) : '') + '<p class="x-small x-muted" style="margin:.75rem 0 0">8 example entries of ' + D.about.members.n + ' members.</p></div>';
    }
    if (t === 'conference') {
      return confBox() + '<div><div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">ERNC27 submissions</h2><span class="x-small x-muted">Abstracts are due on 4 January 2027</span></div>' +
        '<div class="x-table"><table class="x-plaintable" style="min-width:58rem !important"><thead><tr><th>Number</th><th>Title and corresponding author</th><th>Strand and format</th><th>Reviewers</th><th>Review status</th><th>Final decision</th><th>Sent to author</th><th></th></tr></thead><tbody>' + admSubs().map(function (x) {
          var st = S.adm.status[x.no] || x.st, id = 'adm-st-' + x.no;
          return '<tr><td>' + x.no + '</td><td><strong>' + esc(x.title) + '</strong><br><span class="x-muted">' + esc(x.who) + '</span></td><td>' + esc(x.strand) + '<br><span class="x-muted">' + esc(x.format) + '</span></td><td>' +
            (x.rev ? x.rev + ' of 2' : (S.adm.assign[x.no] || []).length ? S.adm.assign[x.no].length + ' assigned' : '<button type="button" class="x-link" data-act="admsub" data-arg="' + x.no + '">Assign</button>') + '</td><td><label class="x-vh" for="' + id + '">Status of ' + x.no + '</label><select id="' + id + '" data-change="' + id + '" class="x-miniselect">' +
            STATI.map(function (o) { return '<option' + (o === st ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select></td><td>' + (S.adm.dec[x.no] ? esc(S.adm.dec[x.no].res) : '<span class="x-muted">Not decided</span>') + '</td><td>' + (S.adm.dec[x.no] && S.adm.dec[x.no].sent ? esc(S.adm.dec[x.no].sent) : '<span class="x-muted">Not sent</span>') + '</td><td><button type="button" class="btn btn-outline x-btn-sm" data-act="admsub" data-arg="' + x.no + '" aria-expanded="' + (S.adm.open === x.no) + '">Open</button></td></tr>';
        }).join('') + '</tbody></table></div>' + (admSubs().filter(function (x) { return x.no === S.adm.open; }).map(subDetail)[0] || '<p class="x-small x-muted" style="margin:0">Three separate things: the review status is internal, the final decision is saved by the editor, and “sent” shows what the author has received.</p>') + '</div>' +
        '<div class="x-grid4">' + [['Programme', 'Place the accepted abstracts into sessions and halls. The result is the programme page.', 'Build the programme', 'the programme builder would open here.'],
          ['Book of Abstracts', 'Made from the accepted abstracts, as pages and as a PDF.', 'Create the book', 'the Book of Abstracts would be created here.'],
          ['Sponsors', 'Add a logo, choose the level and set the link of the sponsor.', 'Manage sponsors', 'logos and levels of the sponsors would be managed here.']].map(function (c) {
          return '<div class="x-card"><h3>' + c[0] + '</h3><p class="x-small x-muted">' + c[1] + '</p>' + nb(c[2], c[3]) + '</div>';
        }).join('') + '<div class="x-card"><h3>Send decisions</h3><p class="x-small x-muted">' + Object.keys(S.adm.dec).filter(function (k) { return S.adm.dec[k] && !S.adm.dec[k].sent; }).length + ' saved and not sent. Sends every saved decision in one go, for example on 15 March 2027.</p><button type="button" class="btn btn-outline x-btn-sm" data-act="admsendall">Send all saved decisions</button></div></div>';
    }
    if (t === 'database') {
      return '<div><h2 class="x-h2">Found automatically, waiting for a check</h2><p class="x-small x-muted" style="margin:-.5rem 0 1rem">From Crossref, OpenAlex and PubMed. Published entries appear in the database and under “New articles” in the member area.</p><ul class="x-list">' + NEWPUBS.slice(0, 4).map(function (p) {
        var st = S.adm.queue[p.id];
        return '<li class="x-item"><div class="x-item-row x-admrow"><div class="x-item-main"><span class="x-item-meta">' + esc(p.v.split(',')[0]) + ' · ' + p.y + '</span><span class="x-item-title">' + esc(p.t) + '</span><span class="x-item-by">' + esc(p.au) + '</span></div><div class="x-row">' +
          (st ? tag(st === 'ok' ? ic('check') + 'Published' : 'Not esports, discarded', st !== 'ok') + '<button type="button" class="x-link" data-act="admqueue" data-arg="' + p.id + '|">Undo</button>'
              : '<button type="button" class="btn btn-primary x-btn-sm" data-act="admqueue" data-arg="' + p.id + '|ok">Publish</button><button type="button" class="btn btn-outline x-btn-sm" data-act="admqueue" data-arg="' + p.id + '|no">Not esports</button>') + '</div></div></li>';
      }).join('') + '</ul></div>' +
        '<div class="x-grid4"><div class="x-card"><h3>Suggested by members</h3><p class="x-small x-muted">1 suggestion waits for a look.</p>' + nb('Open the suggestions', 'the suggested publications would be listed here.') + '</div>' +
        '<div class="x-card"><h3>Reading lists</h3><p class="x-small x-muted">Create a list and choose its publications.</p>' + nb('Edit the lists', 'the reading lists would be edited here.') + '</div>' +
        '<div class="x-card"><h3>Add by hand</h3><p class="x-small x-muted">Enter a DOI. The details are filled in.</p>' + nb('Add a publication', 'a publication would be added by its DOI here.') + '</div></div>';
    }
    if (t === 'content') {
      var block = function (key, title, rows, add, second) {
        var mine = (S.adm.extra[key] || []).map(function (e) { return esc(e[0]) + ' <small class="x-muted">' + esc(e[1] || 'Draft') + '</small>'; });
        return '<div class="x-card"><div class="x-row x-between" style="margin-bottom:.75rem"><h3 style="margin:0">' + title + '</h3><button type="button" class="btn btn-outline x-btn-sm" data-act="admadd" data-arg="' + key + '" aria-expanded="' + (S.adm.add === key) + '">' + add + '</button></div>' +
          (S.adm.add === key ? '<form data-form="admextra" data-key="' + key + '" novalidate class="x-stack" style="gap:.75rem;margin-bottom:1rem">' + fld('ex-title', 'Title', { req: 1 }) + fld('ex-second', second) +
            '<div class="x-row"><button class="btn btn-primary x-btn-sm" type="submit">Save as a draft</button><button type="button" class="btn btn-outline x-btn-sm" data-act="admadd" data-arg="">Cancel</button></div></form>' : '') +
          '<ul class="x-admlist">' + mine.concat(rows).map(function (r) {
          return '<li><span>' + r + '</span><button type="button" class="x-link" data-act="toast" data-arg="' + NOTBUILT + 'this entry would be edited here.">Edit</button></li>';
        }).join('') + '</ul></div>';
      };
      return '<div class="x-two" style="gap:1.5rem;align-items:start">' +
        block('news', 'News', D.news.slice(0, 4).map(function (n) { return esc(n.t) + ' <small class="x-muted">' + esc(n.d) + '</small>'; }), 'New post', 'Date') +
        block('notice', 'Notices for members', D.notices.map(function (n) { return esc(n.t) + ' <small class="x-muted">' + n.docs.length + ' documents</small>'; }), 'New notice', 'Date') +
        block('press', 'Press sources', ['The Esports Radar <small class="x-muted">collected every day</small>', 'The Esports Advocate <small class="x-muted">collected every day</small>'], 'Add a source', 'Web address') + '</div>';
    }
    var todo = [], dns = Object.keys(S.adm.dec).filter(function (k) { return S.adm.dec[k] && !S.adm.dec[k].sent; }).length, noRev = admSubs().filter(function (x) { return !x.rev && !(S.adm.assign[x.no] || []).length; }).length, pr = PROPS();
    if (appsOpen()) todo.push([appsOpen() + ' membership application' + (appsOpen() > 1 ? 's wait' : ' waits') + ' for the board', 'Board', 'Confirm or decline', 'admtab', 'members']);
    if (noRev) todo.push([noRev + ' submission' + (noRev > 1 ? 's have' : ' has') + ' no reviewers yet', 'Conference team', 'Assign reviewers', 'admtab', 'conference']);
    if (dns) todo.push([dns + ' decision' + (dns > 1 ? 's are' : ' is') + ' saved but not sent to the author', 'Final editor', 'Send', 'admtab', 'conference']);
    pr.filter(function (x) { return !S.adm.out[x.id] && stepOf(x) <= 1; }).forEach(function (x) { todo.push(['Proposal “' + esc(x.title) + '” waits for an answer, due 15 April 2027', 'Lecture team', 'Review', 'admlecgo2', 'proposals']); });
    pr.filter(function (x) { return !S.adm.out[x.id] && stepOf(x) >= 3 && stepOf(x) <= 4; }).forEach(function (x) { todo.push(['Materials of ' + esc(x.who) + ' are due two weeks before the lecture', 'Amara Okafor', 'Open the proposal', 'admlecgo2', 'proposals']); });
    Object.keys(S.ws.sent).forEach(function (k) { todo.push(['Changes from a speaker wait for review', 'Editor', 'Review', 'admlecgo2', 'library']); });
    if (progDiffers()) todo.push(['The autumn programme has changes that are not public', 'Lecture team', 'Preview and publish', 'admlecgo2', 'programme']);
    todo.push([(COMING.length - Object.keys(draftProg()).length) + ' lectures of the autumn series have no speaker', 'Lecture team', 'See the programme', 'admlecgo2', 'programme']);
    todo.push(['One teaching unit has no signed permissions yet', 'Amara Okafor', 'Open the library', 'admlecgo2', 'library']);
    if (queueOpen()) todo.push([queueOpen() + ' new articles wait for a check', 'Database Lead', 'Check', 'admtab', 'database']);
    todo.push(['The notice “Board election 2026” names a date that passes on 15 November', 'Editor', 'Update the notice', 'admtab', 'content']);
    var attention = '<div><h2 class="x-h2">Needs attention</h2><ul class="x-list">' + todo.map(function (t) { return '<li class="x-item x-slim"><div class="x-item-row x-admrow"><div class="x-item-main"><span class="x-item-title" style="font-size:.9375rem">' + t[0] + '</span><span class="x-item-by">Owner: ' + t[1] + '</span></div><button type="button" class="btn btn-outline x-btn-sm" data-act="' + t[3] + '" data-arg="' + t[4] + '">' + t[2] + '</button></div></li>'; }).join('') + '</ul></div>';
    var tiles = [['members', appsOpen(), 'Applications to confirm'], ['conference', admSubs().length, 'ERNC27 submissions'], ['database', queueOpen(), 'Articles to check'], ['members', D.about.members.n, 'Members']];
    return attention + '<div class="x-things x-four">' + tiles.map(function (x) { return '<button type="button" class="x-card x-thing" data-act="admtab" data-arg="' + x[0] + '"><b>' + x[1] + '</b><span>' + x[2] + '</span></button>'; }).join('') + '</div>' +
      '<div class="x-two" style="gap:1.5rem;align-items:start"><div class="x-card"><h3>Who sees what</h3><dl class="x-facts x-small">' +
      [['Board', 'Everything in this area'], ['Conference team', 'Submissions, reviewers, programme and sponsors'], ['Reviewers', 'Their assigned abstracts, with comments and a recommendation'], ['Final editor', 'One person for all submissions. Gives the final result and the feedback to the authors'], ['Admin', 'Also sets the rights of the other members'], ['Database Lead', 'The database queue and the reading lists'], ['Lecture team', 'Proposals, the autumn programme and the teaching units'], ['Editors', 'News and notices'], ['Ombudsperson', 'Concerns only. They never appear in this area.']].map(function (d) { return '<div style="grid-template-columns:9rem minmax(0,1fr)"><dt>' + d[0] + '</dt><dd>' + d[1] + '</dd></div>'; }).join('') + '</dl></div>' +
      '<div class="x-card"><h3>Coming up</h3><ul class="x-admlist"><li><span>Call for nominations closes <small class="x-muted">15 November 2026</small></span></li><li><span>Motions for the General Assembly are due <small class="x-muted">9 December 2026</small></span></li><li><span>General Assembly <small class="x-muted">16 December 2026</small></span></li><li><span>Abstract deadline of ERNC27 <small class="x-muted">4 January 2027</small></span></li></ul></div></div>' +
      demo('<p>The applicants and the submissions are invented examples. The roles are a suggestion.</p>');
  }
  function admin() {
    if (!S.admin) {
      return liveBar() + pageHero({ title: 'Admin area' + mk(), text: 'For the board and the people who run the conference, the database and the content.' }) +
        '<div class="container-ern content-wrap"><div class="x-stack" style="max-width:32rem">' + demo('<p>This area needs an admin account. No account is needed here.</p><button type="button" class="btn btn-outline x-btn-sm" data-act="adminin">Enter as a demo admin</button>') + '</div></div>';
    }
    var tabs = [['overview', 'Overview'], ['members', 'Members', appsOpen() || null], ['conference', 'Conference', admSubs().length], ['database', 'Database', queueOpen() || null], ['teaching', 'Teaching', PROPS().length], ['content', 'Content']];
    return liveBar() + '<div class="container-ern content-wrap x-stack">' +
      '<div class="x-row x-between"><div><h1 tabindex="-1" style="font-size:1.75rem">Admin area' + mk() + '</h1><span class="x-small x-muted">Signed in as ' + esc(S.me.name) + ', demo admin</span></div>' +
      '<div class="x-row">' + tag(ic('lock') + 'Board and admins only', 1) + '<a href="#members" data-act="memgo" data-arg="overview" class="btn btn-outline x-btn-sm">Back to the member area</a></div></div>' +
      tabsRow('admtab', tabs, S.admTab, 'Admin area').replace('class="x-tabs"', 'class="x-tabs x-many"') +
      '<div class="x-stack" style="gap:2rem" role="tabpanel">' + admBody() + '</div></div>';
  }
