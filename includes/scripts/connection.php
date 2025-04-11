<?php



//$hostname = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

$conn = new mysqli("localhost","root","","pacpal");


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>