<?php

include("../databases/databaseconnect.php");
$BUTTON = $_POST['BUTTON'];
$ID = $_POST['id'];

if ( $BUTTON == "updateConf" ){
$query_string = "SELECT * from mapping";
$db_handle->exec($query_string);
$result = $db_handle->query($query_string);
while ($row = $result->fetchArray())
      {
		$Zone = $row['zone'];
		$IP = $row['ip'];
		$query_string = "INSERT INTO monitoring (zone, ip, potential_hosts, hosts_up, hosts_up_list, hosts_up_percentage, dScan, tcpScan, udpScan, scan_status, tcpfile_path, udpfile_path, metasploit_upload) VALUES ('$Zone', '$IP', 0, 0, '', '0%','Not started', 'Not started', 'Not started', 0, '', '', 0)";
		$db_handle->exec($query_string);
	  }
    header('Location: index.php');
}

if ( $BUTTON == "resetConf" ){
	$query_string = "DELETE from monitoring";
	$db_handle->exec($query_string);
	$query_string = "DELETE from nmap";
	$db_handle->exec($query_string);
	header('Location: index.php');
}

if ( $BUTTON == "Change" ){
	$query_string = "SELECT * from monitoring WHERE id='$ID'";
	$db_handle->exec($query_string);
	$result = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
      	  {
		$scan_status = $row['scan_status'];
		$dscan = $row['dScan'];
		$tcpscan = $row['tcpScan'];
		if ($scan_status == 0){
		$query_string = "UPDATE monitoring SET dScan='Launched', scan_status=1 WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
		else if ($scan_status == 1 and $dscan == 'Completed'){
		$query_string = "UPDATE monitoring SET tcpScan='Launched', scan_status=2 WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
		else if ($scan_status == 2 and $tcpscan == 'Completed'){
		$query_string = "UPDATE monitoring SET udpScan='Launched', scan_status=3 WHERE id='$ID'";
		$db_handle->exec($query_string);
		}
	  }
	header('Location: index.php');   
}
?>
