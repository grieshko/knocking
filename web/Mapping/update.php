<?php
include("../databases/databaseconnect.php");
$BUTTON = $_POST['BUTTON'];
$Zone = $_POST['zone'];
$IP = $_POST['ip'];
$ID = $_POST['id'];

if ( $BUTTON == "Del" ){
    $query_string = "DELETE FROM mapping";
	$db_handle->exec($query_string);
    header('Location: index.php');
}
if ( $BUTTON == "Rec" ){
	$query_string = "INSERT INTO mapping (zone, ip) VALUES ('$Zone', '$IP')";
	$db_handle->exec($query_string);
    header('Location: index.php');
}
if ( $BUTTON == "Delete" ){
	$query_string = "SELECT * from mapping WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
      	  {
		$query_string = "DELETE from mapping WHERE id='$ID'";
		$db_handle->exec($query_string);

	  }
	header('Location: index.php');   
}
?>
