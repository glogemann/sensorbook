<?php
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
	session_start();
	$_SESSION['accesstoken'] =  $_GET["t"];
	session_write_close();
 
	require_once('config.php');
	// connect to database
	$conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
	if($conn == null) 
    {
        echoresult("ERROR",$timestamp,"db error");
        die();  
    }
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