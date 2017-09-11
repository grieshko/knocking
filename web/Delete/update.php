<?php
include("../databases/databaseconnect.php");
$BUTTON = $_POST['BUTTON'];
$ID = $_POST['id'];
if ( $BUTTON == "resetConf" ){
	$query_string = "DELETE from monitoring";
	$db_handle->exec($query_string);
	$query_string = "DELETE from nmap";
	$db_handle->exec($query_string);
	header('Location: index.php');
}
if ( $BUTTON == "Delete" ){
	$query_string = "SELECT * from monitoring WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
      	  {
		$query_string = "DELETE from monitoring WHERE id='$ID'";
		$db_handle->exec($query_string);
	  }
	header('Location: index.php');   
}
if ( $BUTTON == "DeleteNessus" ){
	$query_string = "SELECT * from Nessus WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
      	  {
		$query_string = "DELETE from Nessus WHERE id='$ID'";
		$db_handle->exec($query_string);
	  }
	header('Location: index.php');   
}
?>
