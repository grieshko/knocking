<?php
include("../databases/databaseconnect.php");
$BUTTON = $_POST['BUTTON'];
$ID = $_POST['id'];

if ( $BUTTON == "Upload" ){
	$query_string = "SELECT * from monitoring WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
	{
		$query_string = "UPDATE monitoring SET metasploit_upload=1 WHERE id='$ID'";
		$db_handle->exec($query_string);
	}
	header('Location: index.php');   
}
if ( $BUTTON == "Unlock" ){
	$query_string = "SELECT * from monitoring WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
	{
		$query_string = "UPDATE monitoring SET metasploit_upload=0 WHERE id='$ID'";
		$db_handle->exec($query_string);
	}
	header('Location: index.php');   
}
if ( $BUTTON == "UploadNessus" ){
	$query_string = "SELECT * from nessus WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
	{
		$query_string = "UPDATE nessus SET scan_status='Upload in progress' WHERE id='$ID'";
		$db_handle->exec($query_string);
	}
	header('Location: index.php');   
}
if ( $BUTTON == "UnlockNessus" ){
	$query_string = "SELECT * from nessus WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
	{
		$query_string = "UPDATE nessus SET scan_status='Report downloaded' WHERE id='$ID'";
		$db_handle->exec($query_string);
	}
	header('Location: index.php');   
}
?>
