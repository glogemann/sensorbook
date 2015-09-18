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

use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

try{
	  $timestamp = time();

    $scon_pipename = $_GET["p"];
    $scon_messagein = $_GET["mi"];
    if(!$scon_pipename) {
      echoresult("ERROR", $timestamp, "no identifier");
      die(); 
    }
    
    $scon_clientkey = $_GET["c"];
    $scon_clientname = $_GET["cn"];
     	
    $conn = new PDO( $dbConnectionString, $user, $pwd);
    if($conn == null) 
    {
        echoresult("ERROR",$timestamp,"db error");
        die();  
    }
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $scon = CheckPermission($conn, $scon_pipename, $scon_clientkey);
    
    if($scon==null) {
      echoresult("ERROR", $timestamp, "Access Denied");
      die(); 
    }
    
    if(!$scon) {
      echo "read again";
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
      $sql->bindValue(1,$scon_pipename); 
      $sql->execute();
      $scon = $sql->fetchAll(); 
    }
    if(count($scon)!=1) {
      echoresult("ERROR", $timestamp, "not found");
      die(); 
    }
    else {
      $scon_masterkey = $scon[0]['masterkey'];
      $micount = $scon[0]['micount'];
      if($micount==null) {
          $micount = 0; 
      }  
      if($scon[0]['clientwritemode']=="AOW") {
        $mi = $scon[0]['messagein'].$scon_messagein;
        if(strlen($mi)>199) {
          echoresult("ERROR", $timestamp, "max buffer");
        }
      }
      else {
        $mi = $scon_messagein;
      }
      
      $micount = $micount+1;
      
      $qry=$conn->prepare("UPDATE serialconnections SET micount=?, date=?, messagein=? WHERE pipename=?");
      $qry->bindValue(1,$micount); 
      $qry->bindValue(2,$timestamp); 
      $qry->bindValue(3,$mi); 
      $qry->bindValue(4,$scon_pipename);
      $result = $qry->execute();
      
      $entity = new Entity();
 
      $entity->setPartitionKey("Message");
      $entity->setRowKey((string)microtime());
      $entity->addProperty("date", EdmType::STRING, (string)$timestamp);
      $entity->addProperty("messageout", EdmType::STRING, ""); 
      $entity->addProperty("messagein", EdmType::STRING, $mi); 
      try{
        $tablename = $scon_pipename.$scon_masterkey;
        $res = $tableRestProxy->insertEntity($tablename, $entity);
      }
      catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here: 
        // http://msdn.microsoft.com/en-us/library/windowsazure/dd179438.aspx
        echo "-----ERROR-----";
        $code = $e->getCode();
        $error_message = $e->getMessage();
        $err = "tableerror".$code.": ".$error_message;
        echoresult("ERROR", $timestamp, $err);
       
        die(); 
      }
    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK", $timestamp, "$timestamp");
?>
