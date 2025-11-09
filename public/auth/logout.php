<?php
session_name('college_portal');
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
