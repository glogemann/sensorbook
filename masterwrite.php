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

use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

try{
    $timestamp = time(); 
    $scon_pipename = $_GET["p"];
    $scon_messageout = $_GET["mo"];
    $scon_masterkey = $_GET["m"]; 
    if(!$scon_pipename) {
      echoresult("ERROR",$timestamp,"no identifier");
      die(); 
    }
    if(!$scon_masterkey) {
      echoresult("ERROR",$timestamp,"no masterkey");
      die(); 
    }
     	
    $conn = new PDO( $dbConnectionString, $user, $pwd);
    if($conn == null) 
    {
        echoresult("ERROR",$timestamp,"db error");
        die();  
    }
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ? AND masterkey = ?");
    $sql->bindValue(1,$scon_pipename); 
    $sql->bindValue(2,$scon_masterkey);
    $sql->execute();
    $scon = $sql->fetchAll(); 
    if(count($scon)!=1) {
      echoresult("ERROR",$timestamp,"not found");
      die(); 
    }
    else {
      $mocount = $scon[0]['mocount'];
      if($mocount==null) {
          $mocount = 0; 
      }   
      if($scon[0]['masterwritemode']=="AOW") {
        $mo = $scon[0]['messageout'].$scon_messageout;
        if(strlen($mo)>999) {
          echoresult("ERROR",$timestamp,"max buffer");
          die(); 
        }
      }
      else {
        $mo = $scon_messageout;
      }

      $mocount = $mocount+1; 
      $qry=$conn->prepare("UPDATE serialconnections SET mocount=?, date=?, messageout=? WHERE pipename=? AND masterkey=?");
      $qry->bindvalue(1,$mocount);
      $qry->bindValue(2,$timestamp); 
      $qry->bindValue(3,$mo); 
      $qry->bindValue(4,$scon_pipename);
      $qry->bindValue(5,$scon_masterkey);
      $result = $qry->execute();
      
      $entity = new Entity();
      $entity->setPartitionKey("Message");
      $entity->setRowKey((string)microtime());
      $entity->addProperty("date", EdmType::STRING, (string)$timestamp);
      $entity->addProperty("messageout", EdmType::STRING, $mo); 
      $entity->addProperty("messagein", EdmType::STRING, ""); 
     try{
        $tablename = $scon_pipename.$scon_masterkey;
        $tableRestProxy->insertEntity($tablename, $entity);
      }
      catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here: 
        // http://msdn.microsoft.com/en-us/library/windowsazure/dd179438.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        $err =  "Tableerror".$code.": ".$error_message;
        echoresult("ERROR",$timestamp,$err);
        die(); 
      }

      
    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK",$timestamp,"");
?>
