<?php 
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