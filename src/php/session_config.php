<?php
session_name('college_portal');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/college-event-portal/public/',
    'domain' => '',
    'secure' => false,
    'httponly' => true
]);
session_start();
