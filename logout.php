<?php
session_start();
unset($_SESSION['user_logged_in'], $_SESSION['user']);
session_destroy();
header('Location: index.php');
exit;
