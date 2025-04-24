<?php
setcookie("token", "", time() - 3600, "/", "", true, true);
header("Location: login.php");
exit;