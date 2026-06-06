<?php
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "petroleum_db"; // Hakikisha hapa pameandikwa hivi bila nafasi (space)

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>