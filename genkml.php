<?php
require('phpsqlajax_dbinfo.php');

// Opens a connection to a MySQL server.

$connection = mysql_connect ($server, $username, $password);

if (!$connection)
{
  die('Not connected : ' . mysql_error());
}
// Sets the active MySQL database.
$db_selected = mysql_select_db($database, $connection);

if (!$db_selected)
{
  die('Can\'t use db : ' . mysql_error());
}

// Selects all the rows in the markers table.
$query = 'SELECT * FROM markers WHERE 1';
$result = mysql_query($query);

if (!$result)
{
  die('Invalid query: ' . mysql_error());
}

// Creates the Document.
$dom = new DOMDocument('1.0', 'UTF-8');

// Creates the root KML element and appends it to the root document.
$node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$parNode = $dom->appendChild($node);

// Creates a KML Document element and append it to the KML element.
$dnode = $dom->createElement('Document');
$docNode = $parNode->appendChild($dnode);

// Creates the two Style elements, one for rat and one for fox, and append the elements to the Document element.
$ratStyleNode = $dom->createElement('Style');
$ratStyleNode->setAttribute('id', 'ratStyle');
$ratIconstyleNode = $dom->createElement('IconStyle');
$ratIconstyleNode->setAttribute('id', 'ratIcon');
$ratIconNode = $dom->createElement('Icon');
$ratHref = $dom->createElement('href', 'rat.png');
$ratIconNode->appendChild($ratHref);
$ratIconstyleNode->appendChild($ratIconNode);
$ratStyleNode->appendChild($ratIconstyleNode);
$docNode->appendChild($ratStyleNode);

$foxStyleNode = $dom->createElement('Style');
$foxStyleNode->setAttribute('id', 'foxStyle');
$foxIconstyleNode = $dom->createElement('IconStyle');
$foxIconstyleNode->setAttribute('id', 'foxIcon');
$foxIconNode = $dom->createElement('Icon');
$foxHref = $dom->createElement('href', 'fox.png');
$foxIconNode->appendChild($foxHref);
$foxIconstyleNode->appendChild($foxIconNode);
$foxStyleNode->appendChild($foxIconstyleNode);
$docNode->appendChild($foxStyleNode);

// Iterates through the MySQL results, creating one Placemark for each row.
while ($row = @mysql_fetch_assoc($result))
{
  // Creates a Placemark and append it to the Document.

  $node = $dom->createElement('Placemark');
  $placeNode = $docNode->appendChild($node);

  // Creates an id attribute and assign it the value of id column.
  $placeNode->setAttribute('id', 'placemark' . $row['id']);

  // Create name, and description elements and assigns them the values of the name and address columns from the results.
  $nameNode = $dom->createElement('name',htmlentities($row['name']));
  $placeNode->appendChild($nameNode);
  $descNode = $dom->createElement('description', $row['address']);
  $placeNode->appendChild($descNode);
  $styleUrl = $dom->createElement('styleUrl', '#' . $row['type'] . 'Style');
  $placeNode->appendChild($styleUrl);

  // Creates a Point element.
  $pointNode = $dom->createElement('Point');
  $placeNode->appendChild($pointNode);

  // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
  $coorStr = $row['lng'] . ','  . $row['lat'];
  $coorNode = $dom->createElement('coordinates', $coorStr);
  $pointNode->appendChild($coorNode);
}

$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml');
echo $kmlOutput;
?>
