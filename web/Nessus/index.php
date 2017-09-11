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
			<button type=submit name=BUTTON value=Rec class='btn btn-warning'><span class='fa fa-spinner fa-spin'></span>Record Nessus Scan</button>
			<button type=submit name=BUTTON value=Del class='btn btn-danger'><span class='fa fa-spinner fa-spin'></span>Reset Scans</button>
			<br/><br/>
			<div class="panel panel-primary">
				<div class="panel-heading">Configuration form</div>
					<table class="table table-striped" align="center">
						<tr>
    						<th>SCAN NAME</th>
    						<th>IP/RANGE IP</th>
    						<th>POLICY</th>
						</tr>
			<tr/>
			<td><textarea class="form-control" rows="3"  name="name"></textarea></td>
			<td><textarea class="form-control" rows="3" name="ip"></textarea></td>
<td>
<select class=form-control name=policy>
<option  value=Basic>Basic Vuln Scan</option>
<option  value=Web>Web Vulnerability</option>
</select>
</td> 
			</tr>
		</FORM>
		</table>
	</div>
		<div class="panel panel-primary">
		<div class="panel-heading">Nessus - IP ranges ready to scan</div>
		<table class="table table-striped" align="center">
		<tr>
    		<th>Scan Name</th>
    		<th>Scan Policy</th>
		<th>IP/RANGE IP</th>
		<th>Scan Status</th>
		<th>Action</th>
		</tr>
	<?php
    	$query_string = "SELECT * from nessus";
	$db_handle->exec($query_string);
	$result     = $db_handle->query($query_string);
	while ($row = $result->fetchArray())     
	{
		$ID = $row['id'];
		$Name = $row['scan_name'];
		$Type = $row['policy_choice'];
		$IP = $row['targets_ip'];
		$Status = $row['scan_status'];
        	echo "<td>" . $Name . "</td>";
		echo "<td>" . $Type . "</td>";
		echo "<td>" . $IP . "</td>";
		echo "<td>" . $Status . "</td>";
		if ($Status == "Scan created" or $Status == "paused" or $Status == "cancelled"){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-success btn-xs'><span class='fa fa-spinner fa-spin'></span>Launch scan </button></td></FORM>";
		}
		elseif ($Status == "Not Started" or $Status == "Running" or $Status == "Launched" or $Status == "Generating Report" or $Status == "Download in progress" or $Status == "Report generated"){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-warning btn-xs'><span class='fa fa-spinner fa-spin'></span>Wait</button></td></FORM>";
		} 
		elseif ($Status == "completed"){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-primary btn-xs'><span class='fa fa-spinner fa-spin'></span>Export Report</button></td></FORM>";
		} 
		elseif ($Status == "Report downloaded"){
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-danger btn-xs'><span class='fa fa-spinner fa-spin'></span>Delete</button></td></FORM>";
		} 
		else{
			echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Change class='btn btn-success btn-xs'><span class='fa fa-spinner fa-spin'></span>Done</button></td></FORM>";
		} 
		echo "</tr>";
		echo "</tr>";
		echo "</tr>";
		echo "</tr>";
      }
	?>
    </FORM></table>
</div>
</div>
<?php include('../footer.html');?>
