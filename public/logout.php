<?php
session_start();
session_unset();
session_destroy();
header("Location: /courses/public/index.php");
exit();