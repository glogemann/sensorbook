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
require_once('config.php');
try{
    $timestamp = time();
    
	$scon_pipename = $_GET["p"];
    if(!$scon_pipename) {
      echoresult("ERROR",$timestamp,"no identifier");
      die(); 
    }

	$scon_key = $_GET["k"];
    if(!$scon_key) {
      echoresult("ERROR",$timestamp,"no key");
      die(); 
    }


	// connect to database
	$conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
	if($conn == null) die("Could not connect to database"); 
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	
    // query pipe
    $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
    $sql->bindValue(1,$scon_pipename); 
    $sql->execute();

    $scon = $sql->fetchAll();
    if(count($scon)==1) {
        $info1 =  "{\"".$scon_key."\" = \"".$scon[0][$scon_key]."\"}";
        echoresult("ok",$timestamp,$info1);
        die(); 
    }
    else {
        echoresult("ERROR",$timestamp,"not found");
        die(); 
    }
}
catch(Exception $e){
    echo $e; 
    die();
}

// timestamp = masterkey; 
echoresult("OK",$timestamp,"");
die(); 	
?>