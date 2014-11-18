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
