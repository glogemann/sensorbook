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
require_once('clientconfig.php'); 

try{
    // check pipename 
    $scon_pipename = $_GET["p"];
    if(!$scon_pipename) {
      echoresult("ERROR", $timestamp, "no pipename");
      die(); 
    }

    // clientkey 
    $scon_clientkey = $_GET["c"];
    //if(!$scon_clientkey) {
    //  echo "ERROR: no clientkey";
    //  die(); 
    //}
    
    $conn = new PDO( $dbConnectionString, $user, $pwd);
    if($conn == null) 
    {
        echoresult("ERROR",$timestamp,"db error");
        die();  
    }
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
	  $timestamp = time(); 
    if(CheckPermission($conn, $scon_pipename, $scon_clientkey)!=null) {
      echoresult("OK", $timestamp, $timestamp);
    }
    else {
      echoresult("ERROR", $timestamp, "access denied");
    }
}
catch(Exception $e){
    echo $e; 
    die();
}
?>
