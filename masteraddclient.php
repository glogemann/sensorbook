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
    $scon_masterkey = $_GET["m"]; 
    if(!$scon_masterkey) {
      echoresult("ERROR",$timestamp,"no masterkey");
      die(); 
    }
    $scon_clientname = $_GET["cn"]; 
    if(!$scon_clientname) {
      echoresult("ERROR",$timestamp,"no clientname");
      die(); 
    }
     	
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ? AND masterkey = ?");
    $sql->bindValue(1,$scon_pipename); 
    $sql->bindValue(2,$scon_masterkey);
    $sql->execute();
    $scon = $sql->fetchAll(); 
    if(count($scon)!=1) {
      echoresult("ERROR",$timestamp,"no found");
      die(); 
    }
    else {
      // check if the name is allready in the permissionlist
      $permissiontablename = "Permission".$scon_pipename; 
      $sql = $conn->prepare("SELECT * FROM ".$permissiontablename." WHERE clientname = ?");
      $sql->bindValue(1,$scon_clientname);
          //print_r($sql); 
          $sql->execute();
          $scon = $sql->fetchAll();
          if(count($scon)>0) {
            echoresult("ERROR",$timestamp,"client allready listed");
            die();  
          }
          else {
 		     $qry = $conn->prepare("INSERT INTO ".$permissiontablename." (clientname,date,clientkey,created) VALUES(?,?,?,?)");
             $qry->bindValue(1,$scon_clientname); 
             $qry->bindValue(2,$timestamp); 
             $qry->bindValue(3,$timestamp); 
             $qry->bindValue(4,$timestamp); 
             $qry->execute(); 
          }

    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK","",$timestamp);
?>