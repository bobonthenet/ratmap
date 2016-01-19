<?php
require('phpsqlajax_dbinfo.php');

// Opens a connection to a MySQL server.
$type = $_GET['type'];
$description = $_GET['description'];
$lat = $_GET['lat'];
$lng = $_GET['lng'];

// Create connection
$conn = new mysqli($server, $username, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO markers (type, address, lat, lng) VALUES (\"$type\", \"$description\", $lat, $lng)";

if ($conn->query($sql) === TRUE) {
    header('Location: index.html');
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>
