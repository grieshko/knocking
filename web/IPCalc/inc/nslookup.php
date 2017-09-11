<?php
	if(isset($_GET['ip']) && $_GET['ip']!="")
	{
		$domain = $_GET['ip'];
		$domain = trim($domain);
		if(substr(strtolower($domain), 0, 7) == "http://") $domain = substr($domain, 7); // remove http:// if included
		if(substr(strtolower($domain), 0, 4) == "www.") $domain = substr($domain, 4);//remove www from domain
		if(preg_match("/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/",$domain))
		{
			$result = gethostbyaddr($domain);
		}
		elseif(preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i",$domain))
		{
			$result = gethostbynamel($domain);
		}
		if(is_array($result))
		{
			foreach ($result as $value){
				print $value."<br>";
			}
		}
		else
		{
			print $result;
		}
	}
	else
	{
		print '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Champ vide';
	}
?>


