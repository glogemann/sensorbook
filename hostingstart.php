<?php
	session_start();
	$_SESSION['accesstoken'] =  $_GET["t"];
	session_write_close();
 
	require_once('config.php');
	// connect to database
	$conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
	if($conn == null) die("Could not connect to database"); 
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sensorbook Dashboard</title>
<link href="sensorbook.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.auto-style1 {
	background-color: #CCCCCC;
}
</style>
</head>
<body>
	<h1>Dashboard:</h1>
	<hr />
	<table>
	<tr>
		<td style="width: 100px" class="auto-style1"><x></x></td>
		<td style="width: 100px" class="auto-style1"><x>Pipename</x></td>
		<td style="width: 100px" class="auto-style1"><x>Created</x></td>
		<td style="width: 100px" class="auto-style1"><x>Last Update</x></td>
		<td style="width: 100px" class="auto-style1"><x>out Count</x></td>
		<td style="width: 100px" class="auto-style1"><x>in Count</x></td>
		<td style="width: 60px" class="auto-style1"><x>Client Mode</x></td>
	</tr>
	</table>	
	<hr />	
	
	<?php
		$sql = $conn->prepare("SELECT * FROM serialconnections ORDER BY date DESC");
    	$sql->execute();
    	$scon = $sql->fetchAll(); 
		for($i=0; $i<count($scon); $i++) {
			//echo "<table style=\"width: 100%\">\n\r";
			echo "<table>\n\r";
			echo "<tr>\n\r";
			echo "<td style=\"width: 100px\"><img alt=\"\" height=\"30\" src=\"\Assets\Sensor.png?p=".$scon[$i]['pipename']."\" width=\"30\"><br><a href=\"viewsensor.php?p=".$scon[$i]['pipename']."\">View Sensor</a></td>\n\r";
  			echo "<td style=\"width: 100px\">".$scon[$i]['pipename']."</td>\n\r";
  			echo "<td style=\"width: 100px\">".date('Y.d.m H:i:s T', $scon[$i]['created'])."</td>\n\r";
  			echo "<td style=\"width: 100px\">".date('Y.d.m H:i:s T', $scon[$i]['date'])."</td>\n\r";
  			echo "<td style=\"width: 100px\">".$scon[$i]['mocount']."</td>\n\r";
  			echo "<td style=\"width: 100px\">".$scon[$i]['micount']."</td>\n\r";
  			echo "<td style=\"width: 60px\">".$scon[$i]['clientmode']."</td>\n\r";
   			echo "</tr>\n\r";  
  			echo "</table>\n\r";
  			echo "<hr />\n\r"; 		
		}
	?>

</body>
</html>