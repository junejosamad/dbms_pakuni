<?php
require_once 'config/session.php';

clearUserSession();
header("Location: index.php");
exit();
?> 