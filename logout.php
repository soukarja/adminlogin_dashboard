<?php
// Initialize the session

require 'dependencies.php';

logout();

header("location: $auth_url");
exit;
