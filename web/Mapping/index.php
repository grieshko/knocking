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
			<button type=submit name=BUTTON value=Rec class='btn btn-warning'><span class='fa fa-spinner fa-spin'></span>Record Conf</button>
			<button type=submit name=BUTTON value=Del class='btn btn-danger'><span class='fa fa-spinner fa-spin'></span>Reset Conf</button>
			<br/><br/>
			<div class="panel panel-primary">
				<div class="panel-heading">Configuration form</div>
					<table class="table table-striped" align="center">
						<tr>
    						<th>Zone</th>
    						<th>IP/RANGE IP</th>
						</tr>
			<tr/>
			<td><textarea class="form-control" rows="3"  name="zone"></textarea></td>
			<td><textarea class="form-control" rows="3" name="ip"></textarea></td>
			</tr
		</FORM>
		</table>
	</div>
		<div class="panel panel-primary">
		<div class="panel-heading">Configuration - IP ranges ready to scan</div>
		<table class="table table-striped" align="center">
		<tr>
    		<th>Zone</th>
		<th>IP/RANGE IP</th>
    		<th>Delete</th>
		</tr>
	<?php
    $query_string = "SELECT * from mapping";
	$db_handle->exec($query_string);
	$result     = $db_handle->query($query_string);
	while ($row = $result->fetchArray())     
	{
		$Zone = $row['zone'];
		$IP = $row['ip'];
		$ID = $row['id'];
        echo "<td>" . $Zone . "</td>";
		echo "<td>" . $IP . "</td>";
		echo "<td><FORM ACTION='update.php' METHOD='POST'><input type='hidden' name='id' value=" . $ID . "><button type=submit name=BUTTON value=Delete class='btn btn-danger btn-xs'><span class='fa fa-spinner fa-spin'></span>Delete</button></td></FORM>";
		echo "</tr>";
      }
	?>
  </table>
</div>
</div>
<?php include('../footer.html');?>
