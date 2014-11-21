// The MIT License (MIT) 
 
// Copyright (c) 2014 Microsoft DX  
 
// Permission is hereby granted, free of charge, to any person obtaining a copy 
// of this software and associated documentation files (the "Software"), to deal 
// in the Software without restriction, including without limitation the rights 
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
// copies of the Software, and to permit persons to whom the Software is 
// furnished to do so, subject to the following conditions: 

// The above copyright notice and this permission notice shall be included in all 
// copies or substantial portions of the Software. 
 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
// SOFTWARE. 
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