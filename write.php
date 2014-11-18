﻿<?php
require_once('clientconfig.php'); 

use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

try{
    $scon_pipename = $_GET["p"];
    $scon_messagein = $_GET["mi"];
    if(!$scon_pipename) {
      die("ERROR:no identifier"); 
    }
    
    $scon_clientkey = $_GET["c"];
    $scon_clientname = $_GET["cn"];
     	
	  $timestamp = time();
    
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $scon = CheckPermission($conn, $scon_pipename, $scon_clientkey);
    
    if($scon==null) 
      die("ERROR:Access Denied1");
    
    if(!$scon) {
      echo "read again";
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
      $sql->bindValue(1,$scon_pipename); 
      $sql->execute();
      $scon = $sql->fetchAll(); 
    }
    if(count($scon)!=1) {
      die("ERROR:not found"); 
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
          die("ERROR:max buffer"); 
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
        echo $code.": ".$error_message;
        die(); 
      }
    }
}
catch(Exception $e){
    die(print_r($e));
}
echo "ok:".$timestamp;
?>
