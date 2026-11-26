<?php

use Laravel\Fortify\Features;

// Sign-in for members (R0-05). Our own routes live in routes/web.php under /members,
// because Fortify's own routes are switched off in FortifyServiceProvider.
// Public registration is off: membership goes through the application (R1-09).

return [

    'guard' => 'web',

    'passwords' => 'users',

    'username' => 'email',

    'email' => 'email',

    'lowercase_usernames' => true,

    // Where a member lands after signing in.
    'home' => '/members',

    'prefix' => '',

    'domain' => null,

    'middleware' => ['web'],

    // null: Fortify's own lock applies (5 failed attempts per e-mail address and IP,
    // then 60 seconds), and it answers with a normal form error instead of a bare 429 page.
    'limiters' => [
        'login' => null,
    ],

    'views' => true,

    'features' => [
        Features::resetPasswords(),
    ],

];
