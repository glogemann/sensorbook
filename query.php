<?php
	session_start();
	$_SESSION['accesstoken'] =  $_GET["t"];
	session_write_close();
 
	require_once('config.php');
	// connect to database
	$conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
	if($conn == null) die("Could not connect to database"); 
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	
	$sql = $conn->prepare("SELECT * FROM serialconnections ORDER BY date DESC");
    $sql->execute();
	
	$rows = array();
	$scon = $sql->fetchAll(); 
	for($i=0; $i<count($scon); $i++) {
    	$rows[] = $scon[$i];
	}
	print json_encode($rows);
?>