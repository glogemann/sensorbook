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
    // check pipename 
    
    $timestamp = time();
     
    $scon_pipename = $_GET["p"];
    if(!$scon_pipename) {
      echoresult("ERROR",$timestamp,"no identifier");
      die(); 
    }
    
    $scon_masterkey = $_GET["m"];
    
    // set client access mode
    // "CRO" client read only 
    // "CROA" client read only with authentication
    // "CRW" client read/write (default) 
    // "CRWA" client read/write with authentication 
    $scon_clientmode = $_GET["ca"]; 
    if(!$scon_clientmode) {
       $scon_clientmode = "CRW";
    }
    else {
      if($scon_clientmode == "CRO") {
      }
      else if($scon_clientmode == "CROA") {
      }
      else if($scon_clientmode == "CRW") {
      }
      else if($scon_clientmode == "CRWA") {
      } else {
          echoresult("ERROR",$timestamp,"illegal clientacccessmode");
          die(); 
      }
    }
    
    // set client read mode
    // "FOR" read on read (default) 
    // "NFOR" no read on read
    $scon_clientreadmode = $_GET["cr"]; 
    //echo $scon_clientreadmode;
    if(!$scon_clientreadmode) {
      $scon_clientreadmode = "FOR"; 
    }
    else {
      if($scon_clientreadmode=="FOR") {
      }
      else if($scon_clientreadmode=="NFOR") {
      } else {
          echoresult("ERROR",$timestamp,"illegal clientreadmode");
          die(); 
      }
    }
    
    // set master read mode
    // "FOR" read on read (default)
    // "NFOR" no read on read
    $scon_masterreadmode = $_GET["mr"]; 
    if(!$scon_masterreadmode) {
      $scon_masterreadmode = "FOR"; 
    }
    else {
      if($scon_masterreadmode=="FOR") {
      }
      else if($scon_masterreadmode=="NFOR") {
      } else {
          echoresult("ERROR",$timestamp,"illegal masterreadmode");
          die(); 
      }
    }
    
    // set client write mode
    // "ROW" Replace on Write 
    // "AOW" Append on write (default) 
    $scon_clientwritemode = $_GET["cw"]; 
    if(!$scon_clientwritemode) {
      $scon_clientwritemode = "AOW"; 
    }
    else {
      if($scon_clientwritemode=="ROW") {
      }
      else if($scon_clientwritemode=="AOW") {
      } else {
          echoresult("ERROR",$timestamp,"illegal clientwritemode");
          die(); 
      }
    }
    
    // set master write mode
    // "ROW" Replace on Write 
    // "AOW" Append on write (default) 
    $scon_masterwritemode = $_GET["mw"]; 
    if(!$scon_masterwritemode) {
      $scon_masterwritemode = "AOW"; 
    }
    else {
      if($scon_masterwritemode=="ROW") {
      }
      else if($scon_masterwritemode=="AOW") {
      } else {
          echoresult("ERROR",$timestamp,"illegal masterwritemode");
          die(); 
      }
    }
     	
	

    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    // is masterkey is present this is an reopen of an existing pipe
    if(!$scon_masterkey) {
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
      $sql->bindValue(1,$scon_pipename); 
    }
    else {
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ? AND masterkey = ?");
      $sql->bindValue(1,$scon_pipename); 
      $sql->bindValue(2,$scon_masterkey); 
    }
    $sql->execute();
    $scon = $sql->fetchAll(); 
    
    if(count($scon)>0) {
      if(!$scon_masterkey) {
        echoresult("ERROR",$timestamp,"allready open");
        die(); 
      }
      else {
        // reopen 
        echoresult("OK",$timestamp,$scon_masterkey);
        die(); 
      }
    }
    else {
      // no pipe is present under this name
      // create a new one 
 		  $qry = $conn->prepare("INSERT INTO serialconnections (pipename,date,masterkey,clientmode,clientreadmode,clientwritemode,masterreadmode,masterwritemode,created) VALUES(?,?,?,?,?,?,?,?,?)");
      $qry->bindValue(1,$scon_pipename); 
      $qry->bindValue(2,$timestamp); 
      $qry->bindValue(3,$timestamp); 
      $qry->bindValue(4,$scon_clientmode); 
      $qry->bindValue(5,$scon_clientreadmode);
      $qry->bindValue(6,$scon_clientwritemode);
      $qry->bindValue(7,$scon_masterreadmode);
      $qry->bindValue(8,$scon_masterwritemode);
      $qry->bindValue(9,$timestamp); 
      $qry->execute(); 
      
      //create log table;  
      try {
        // Create table.
        $tableRestProxy->createTable($scon_pipename.$timestamp);
      }
      catch(ServiceException $e){
          echo $e; 
          die($error_message);
      }

      try{      
        $permissiontablename = "Permission".$scon_pipename;
        //echo $permissiontablename;
        $qry1 = $conn->prepare("CREATE TABLE ".$permissiontablename." (
                              id INT NOT NULL IDENTITY(1,1) 
                              PRIMARY KEY(id),
                              clientname VARCHAR(40),
                              clientkey VARCHAR(40),
                              clientpermissioninfo VARCHAR(100), 
                              clienttrigger VARCHAR(500), 
                              options VARCHAR(100),
                              clientmode VARCHAR(10), 
                              clientreadmode VARCHAR(10),
                              clientwritemode VARCHAR(10),
                              created VARCHAR(20),
                              date VARCHAR(20))"); 
        $qry1->bindValue(1,$permissiontablename);
        //print_r($qry1);
        $qry1->execute(); 
      }
      catch(Exception $e){
        echo $e; 
        die();
      } 
    }
}
catch(Exception $e){
    echo $e; 
    die();
}

// timestamp = masterkey; 
echoresult("OK",$timestamp,$timestamp);
die(); 
?>