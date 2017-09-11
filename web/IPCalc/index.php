<!DOCTYPE html>
<html lang="en">
<?php
include('../header.html');
?>
<?php include('./inc/myip.php'); ?>
  <body>
    <div class="container">
      <!-- Main component for a primary marketing message or call to action -->
      <br/><br/><br/><br/>
	  	<!--Mon ip : <?php //print get_client_ip();?>-->
	<div class="row">
	 <div class="col-md-4">
	  <div class="thumbnail panel panel-default">
		  <div class="panel-heading">Whois</div>
		  <div class="panel-body">
		  <p>
			<form method="post" action="#" role="form" id="whois" class="form-inline">
			  <div class="form-group"><label for="site" class="sr-only">Enter Domain / IP / ASN</label> <input type="text" name="site" id="site" class="form-control" placeholder="Domain / IP / ASN"></div>
			  <div class="form-group">
			  <input type="submit" name="Submit" value="Whois" class="btn btn-primary">
			  </div><br/><br/>
			  <div id="rs_whois" ></div>
			</form>
		</p>
		  </div>
		</div>
	  </div>
	  <div class="col-md-4">
	  <div class="thumbnail panel panel-default">
		  <div class="panel-heading">
			<h3 class="panel-title">NSLookup</h3>
		  </div>
		  <div class="panel-body">
			<p>
				<form method="post" action="#" role="form" id="nslookup" class="form-inline">
				  <div class="form-group"><label for="site_lookup" class="sr-only">Enter Domain / IP </label> <input type="text" name="site_lookup" id="site_lookup" class="form-control" placeholder="Domain / IP "></div>
				  <div class="form-group">
				  <input type="submit" name="Submit" value="Lookup" class="btn btn-primary">
				  </div><br/><br/>
				  <div id="rs_lookup" ></div>
			</form>
			</p>
		  </div>
		</div>
	 </div>
	 <div class="col-md-4">
	  <div class="thumbnail panel panel-default">
		  <div class="panel-heading">
			<h3 class="panel-title">Network calculator</h3>
		  </div>
		  <div class="panel-body">
			<p>
				<form method="post" action="#" role="form" id="calculette" class="form-inline">
				  <div class="form-group">
					<label for="site_lookup" class="sr-only">Enter Domain / IP </label> 
					<input type="text" name="IP" id="IP" class="form-control" placeholder="Adresse IP">
					<input type="text" name="MASK" id="MASK" class="form-control" placeholder="Masque /21, /22, etc...">
				  </div>
				  <div class="form-group">
				  <input type="submit" name="Submit" value="Calcul" class="btn btn-primary">
				  </div><br/><br/>
				  <div id="rs_calculette" ></div>
			</form>
			</p>
		  </div>
		</div>
	 </div>
	 </div>
    </div> <!-- /container -->
	<?php include('../footer.html'); ?>
	<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="./jquery/jquery-2.1.1.js"></script>
<script src="./bootstrap/js/bootstrap.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="./datatables/media/css/jquery.dataTables.css">
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="./datatables/media/js/jquery.dataTables.js"></script>

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
	<script type="text/javascript">
$(document).ready(function(){
	$("#whois").submit(function(){
		var site=$("#site").val();
		$.ajax({
			url:"./inc/whois.php",
			type: "GET",
			data: {ip:site},
			dataType: "html",
			beforeSend: function(){$("#rs_whois").html('<img src="img/ajax-loader.gif" />');},
			success:function(d){$("#rs_whois").html(d); $("#rs_whois").addClass( "alert alert-danger" );
			},
			error: function(){ $("#rs_whois").html('<strong>ERROR:</strong> Please try again later');}
			});
		return false;
	});
})

$(document).ready(function(){
	$("#nslookup").submit(function(){
		var site=$("#site_lookup").val();
		$.ajax({
			url:"./inc/nslookup.php",
			type: "GET",
			data: {ip:site},
			dataType: "html",
			beforeSend: function(){$("#rs_lookup").html('<img src="img/ajax-loader.gif" />');},
			success:function(d){$("#rs_lookup").html(d); $("#rs_lookup").addClass( "alert alert-danger" );
			},
			error: function(){ $("#rs_lookup").html('<strong>ERROR:</strong> Please try again later');}
			});
		return false;
	});
})

$(document).ready(function(){
	$("#calculette").submit(function(){
		var ip=$("#IP").val();
		var mask=$("#MASK").val();
		$.ajax({
			url:"./inc/calculette.php",
			type: "GET",
			data: {ip:ip, mask:mask},
			dataType: "html",
			beforeSend: function(){$("#rs_calculette").html('<img src="img/ajax-loader.gif" />');},
			success:function(d){$("#rs_calculette").html(d); $("#rs_calculette").addClass( "alert alert-danger" );
			},
			error: function(){ $("#rs_calculette").html('<strong>ERROR:</strong> Please try again later');}
			});
		return false;
	});
})
</script>
  </body>
</html>
