<?php
setcookie("token", "", time() - 3600, "/", "", true, true);
session_destroy();
header("Location: dashboard.php");
exit();