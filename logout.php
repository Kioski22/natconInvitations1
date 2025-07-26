<?php
session_start();
session_destroy();
header("Location: login.php"); // Change this if your login is in a different file
exit();
