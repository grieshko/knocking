<?php
include('../header.html');
include("../databases/databaseconnect.php");
?>
      <!-- Jumbotron -->
      <div class="jumbotron">
      </div>
<div class="container">
    <div class="row">
		</FORM>
		<br/>
		<div class="panel panel-primary">
		<div class="panel-heading">Metasploit - NMAP please launch a msfconsole with the command "load msgrpc Pass=abc123" before</div>
		<table class="table table-striped" align="center">
		<tr>
    		<th>Zone</th>
		<th>IP/RANGE IP</th>
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
		$Up = $row['metasploit_upload'];
        	echo "<td>" . $Zone . "</td>";
		echo "<td>" . $IP . "</td>";
		echo "<td>" . $DS . "</td>";
		echo "<td>" . $TSc . "</td>";
		echo "<td>" . $USc . "</td>";
		if ($Up == 0){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Upload class='btn btn-primary btn-xs'><span class='fa fa-spinner fa-spin'></span>Upload to Metasploit</button></td></FORM>";
		}
		if ($Up == 1){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Upload class='btn btn-success btn-xs'><span class='fa fa-spinner fa-spin'></span>Upload in progress</button></td></FORM>";
		}
		if ($Up == 2){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Unlock class='btn btn-warning btn-xs'><span class='fa fa-spinner fa-spin'></span>Unlock</button></td></FORM>";
		}
		echo "</tr>";
		echo "</tr>";
      	   }
	?>
      </div>
    </FORM></table>
      </div>
      </div>
    <div class="row">
		<br/>
		<div class="panel panel-primary">
		<div class="panel-heading">Metasploit - NESSUS please launch a msfconsole with the command "load msgrpc Pass=abc123" before</div>
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
		if ($SS == "Report downloaded"){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=UploadNessus class='btn btn-primary btn-xs'><span class='fa fa-spinner fa-spin'></span>Upload to Metasploit</button></td></FORM>";
		}
		else{
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=UnlockNessus class='btn btn-warning btn-xs'><span class='fa fa-spinner fa-spin'></span>Unlock</button></td></FORM>";
		}
		echo "</tr>";
      	   }
	?>
    </FORM></table>
</div>
</div>
<?php include('../footer.html');?>
