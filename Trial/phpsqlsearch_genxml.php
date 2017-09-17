<?php
require("phpsqlsearch_dbinfo.php");

// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Opens a connection to a mySQL server
$conn=new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}


// Search the rows in the markers table
$query = "SELECT address, name, lat, lng, ( 3959 * acos( cos( radians('$center_lat') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('$center_lng') ) + sin( radians('$center_lat') ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < '$radius' ORDER BY distance LIMIT 0 , 20";
$result = $conn->query($query);
echo $query;
if ($result->num_rows == 0) {
    die("Invalid query: " . $conn->connect_error);
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = $result->fetch_assoc()){
    $node = $dom->createElement("marker");
    $newnode = $parnode->appendChild($node);
    $newnode->setAttribute("name", $row['name']);
    $newnode->setAttribute("address", $row['address']);
    $newnode->setAttribute("lat", $row['lat']);
    $newnode->setAttribute("lng", $row['lng']);
    $newnode->setAttribute("distance", $row['distance']);
}

echo $dom->saveXML();
?>