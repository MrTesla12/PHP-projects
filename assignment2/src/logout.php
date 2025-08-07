<?php
// src/logout.php
session_start();
session_unset();     // remove all session variables
session_destroy();   // destroy the session itself
header('Location: /assignment2/index.php');
exit;
