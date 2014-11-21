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
require_once('config.php'); 

try{
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
     	
	  $timestamp = time(); 

    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ? AND masterkey = ? ");
    $sql->bindValue(1,$scon_pipename); 
    $sql->bindValue(2,$scon_masterkey); 
    $sql->execute();
    $scon = $sql->fetchAll(); 
    if(count($scon)>0) {
      // found -> delete 
      $qry = $conn->prepare("DELETE FROM serialconnections WHERE pipename = ?");
      $qry->bindValue(1,$scon_pipename); 
      $qry->execute();
      try {
        // Create table.
        $tableRestProxy->deleteTable($scon_pipename.$scon_masterkey);
      }
      catch(ServiceException $e) {
          echo $e; 
          die();
      }
      
      $permissiontablename = "Permission".$scon_pipename;
      $qry = $conn->prepare("DROP TABLE ".$permissiontablename); 
      $qry->execute(); 
    }
    else {
      echoresult("ERROR",$timestamp,"not found");
      die(); 
    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK",$timestamp,"");
die(); 
?>
