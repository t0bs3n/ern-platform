<?php

/*
| Addresses of the old website and where they lead now (R1-18). Permanent redirects (301).
| Add a line per old page: 'old/address' => '/new/address'. Leave out the slash at the start
| of the old address. The old news addresses (/2026/08/10/post-name/) are handled in
| routes/web.php and lead to the post of the same name.
|
| Not here yet, because their new page does not exist:
|   concern/     -> /report-a-concern   (R1-17)
|   lectures/    -> /teaching           (release 3)
|   submission/  stays on the old site until our own submission form exists (release 5)
|
| Old addresses that are the same on the new site (about, news, imprint, get-involved)
| need no line: they answer directly.
*/

return [
    'conference' => '/conferences',
    'getinvolved' => '/get-involved',
    'ernc21-summary' => '/conferences/ernc21',
    'ernc22-summary' => '/conferences/ernc22',
    'ernc24-summary' => '/conferences/ernc24',
    'ernc25-summary' => '/conferences/ernc25',
];
