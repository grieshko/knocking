<?php
	$search = $_GET['search'];	
	//$critical = $_GET['critical'];	
	
	$json = file_get_contents('https://technet.microsoft.com/security/bulletin/services/GetBulletins?searchText='.$search.'&sortField=4&sortOrder=1&currentPage=1&bulletinsPerPage=50&locale=en-US');
	
	$obj = json_decode($json);
	
	$test = $obj->{'b'};
	
	print '<table class="table" id="patch_mgt_table" name="patch_mgt_table">';
	print '<theader><th>Date (FR)</th><th>ID</th><th>KB #</th><th>Titre</th><th>Severité</th></theader>';
	foreach ($obj->{'b'} as $value)
	{
		print "<tr><td>".strftime("%d/%m/%Y", strtotime($value->{'d'}))."</td>";
		print "<td>".$value->{'Id'}."</td>";
		print "<td>".$value->{'KB'}."</td>";
		print "<td><a href='https://technet.microsoft.com/en-US/library/security/".$value->{'Id'}."' target='_blank'>".$value->{'Title'}."</a></td>";
		print "<td>".$value->{'Rating'}."</td></tr>";
	}
	print "</table>";
?>


