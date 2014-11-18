<?php
require_once('config.php'); 

try{
	  $timestamp = time(); 

    $scon_pipename = $_GET["p"];
    $scon_channel = $_GET["ch"];
    $scon_masterkey = $_GET["m"]; 
    if(!$scon_pipename) {
      echoresult("ERROR",$timestamp,"no indentifier");
      die(); 
    }
    if(!$scon_masterkey) {
      echoresult("ERROR",$timestamp,"no masterkey");
      die(); 
    }
    if(!$scon_channel) {
      echoresult("ERROR",$timestamp,"no channel");
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
      die(print_r("Error:not found")); 
    }
    else {
      $qry=$conn->prepare("UPDATE serialconnections SET date=?, mastertrigger=? WHERE pipename=? AND masterkey=?");
      $qry->bindValue(1,$timestamp); 
      $qry->bindValue(2,$scon_channel); 
      $qry->bindValue(3,$scon_pipename);
      $qry->bindValue(4,$scon_masterkey);
      $result = $qry->execute();
    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK",$timestamp,"");
?>
