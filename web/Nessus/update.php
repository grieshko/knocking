<?php
include("../databases/databaseconnect.php");
$BUTTON = $_POST['BUTTON'];
$Name = $_POST['name'];
$IP = $_POST['ip'];
$Policy = $_POST['policy'];
$ID = $_POST['id'];

if ( $BUTTON == "Del" ){
    $query_string = "DELETE FROM nessus";
	$db_handle->exec($query_string);
    header('Location: index.php');
}
if ( $BUTTON == "Rec" ){
	$query_string = "INSERT INTO nessus (scan_id, scan_name, targets_ip, policy_choice, scan_status) VALUES ('0', '$Name', '$IP', '$Policy', 'Not Started')";
	$db_handle->exec($query_string);
    header('Location: index.php');
}
if ( $BUTTON == "Change" ){
	$query_string = "SELECT * from nessus WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
      	  {
		$scan_status = $row['scan_status'];
		if ($scan_status == "Scan created"){
		$query_string = "UPDATE nessus SET scan_status='Launched' WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
		else if ($scan_status == "completed"){
		$query_string = "UPDATE nessus SET scan_status='Generating Report' WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
		else if ($scan_status == "paused" or $scan_status == "cancelled"){
		$query_string = "UPDATE nessus SET scan_status='Launched' WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
		else if ($scan_status == "Report generated"){
		$query_string = "UPDATE nessus SET scan_status='Download in progress' WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
		else if ($scan_status == "Report downloaded"){
		$query_string = "DELETE FROM nessus WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
	  }
	header('Location: index.php');   
}
?>
