<?php
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
     	
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
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
        echo $code.": ".$error_message;
        die(); 
      }

      
    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK",$timestamp,"");
?>