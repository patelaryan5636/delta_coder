<?php
session_start();
session_destroy(); // Destroy the session
header("Location: sign-in"); // Redirect to sign-in page    \
?>