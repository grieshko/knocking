<?php
include('../header.html');
include("../databases/databaseconnect.php");
?>
      <!-- Jumbotron -->
      <div class="jumbotron">
      </div>
<div class="container">
    <div class="row">
			<FORM ACTION='update.php' METHOD='POST'>
			<button type=submit name=BUTTON value=updateConf class='btn btn-warning'><span class='fa fa-spinner fa-spin'></span>Update Conf</button>
		</FORM>
		<br/>
		<div class="panel panel-primary">
		<div class="panel-heading">NMAP - Monitoring Scans</div>
		<table class="table table-striped" align="center">
		<tr>
    		<th>Zone</th>
		<th>IP/RANGE IP</th>
		<th>#Pot. Hosts</th>
		<th>#Hosts UP</th>
		<th>% UP</th>
		<th>Discovery Scan</th>
		<th>TCP PortScan</th>
		<th>UDP PortScan</th>
		<th>Action</th>
		</tr>
	<?php
    $query_string = "SELECT * from monitoring";
	$result     = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
	  {
		$ID = $row['id'];
		$Zone = $row['zone'];
		$IP = $row['ip'];
		$DS = $row['dScan'];
		$PH = $row['potential_hosts'];
		$HU = $row['hosts_up'];
		$HP = $row['hosts_up_percentage'];
		$SS = $row['scan_status'];
		$TSc = $row['tcpScan'];
		$USc = $row['udpScan'];
        echo "<td>" . $Zone . "</td>";
		echo "<td>" . $IP . "</td>";
		echo "<td>" . $PH . "</td>";
		echo "<td>" . $HU . "</td>";
		echo "<td>" . $HP . "</td>";
		echo "<td>" . $DS . "</td>";
		echo "<td>" . $TSc . "</td>";
		echo "<td>" . $USc . "</td>";
		if ($SS == 0){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-default btn-xs'><span class='fa fa-spinner fa-spin'></span>Discovery</button></td></FORM>";
		}
		if ($SS == 1){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-success btn-xs'><span class='fa fa-spinner fa-spin'></span>TCP PortScan</button></td></FORM>";
		} 
		if ($SS == 2){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-primary btn-xs'><span class='fa fa-spinner fa-spin'></span>UDP PortScan</button></td></FORM>";
		} 
		if ($SS == 3){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-warning btn-xs'><span class='fa fa-spinner fa-spin'></span>Locked</button></td></FORM>";
		} 
		echo "</tr>";
		echo "</tr>";
      }
	?>
    </FORM></table>
</div>
</div>
<?php include('../footer.html');?>
