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
			<button type=submit name=BUTTON value=resetConf class='btn btn-danger'><span class='fa fa-spinner fa-spin'></span>Delete All Records</button>
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
		$USc = $row['tcpScan'];
		$TSc = $row['udpScan'];
       		echo "<td>" . $Zone . "</td>";
		echo "<td>" . $IP . "</td>";
		echo "<td>" . $PH . "</td>";
		echo "<td>" . $HU . "</td>";
		echo "<td>" . $HP . "</td>";
		echo "<td>" . $DS . "</td>";
		echo "<td>" . $TSc . "</td>";
		echo "<td>" . $USc . "</td>";
		echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Delete class='btn btn-danger btn-xs'><span class='fa fa-spinner fa-spin'></span>Delete</button></td></FORM>";
		echo "</tr>";
		echo "</tr>";
      	  }
	?>
    </FORM></table>
      </div>
      </div>
    <div class="row">
		<br/>
		<div class="panel panel-primary">
		<div class="panel-heading">Nessus </div>
		<table class="table table-striped" align="center">
		<tr>
    		<th>Scan Name</th>
		<th>Scan Policy</th>
		<th>IP/Range IP</th>
		<th>Scan Status</th>
		<th>Action</th>
		</tr>
	<?php
    	$query_string = "SELECT * from nessus";
	$result     = $db_handle->query($query_string);
	while ($row = $result->fetchArray())
	  {
		$ID = $row['id'];
		$Name = $row['scan_name'];
		$PO = $row['policy_choice'];
		$IP = $row['targets_ip'];
		$SS = $row['scan_status'];
        	echo "<td>" . $Name . "</td>";
		echo "<td>" . $PO . "</td>";
		echo "<td>" . $IP . "</td>";
		echo "<td>" . $SS . "</td>";
		echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=DeleteNessus class='btn btn-danger btn-xs'><span class='fa fa-spinner fa-spin'></span>Delete</button></td></FORM>";
		echo "</tr>";
      	   }
	?>
    </FORM></table>
</div>
</div>
<?php include('../footer.html');?>
