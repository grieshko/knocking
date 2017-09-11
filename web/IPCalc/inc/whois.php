<?php
	if(isset($_GET['ip']))
	{
		require("whoisClass.php");
		$whois=new Whois;
		echo nl2br($whois->whoislookup($_GET['ip']));
	}
?>


