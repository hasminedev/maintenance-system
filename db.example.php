<?php
$conn = new mysqli("your_host", "your_user", "your_password", "your_database");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>