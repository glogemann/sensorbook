<?php
require_once('clientconfig.php'); 

try{
    $scon_pipename = $_GET["p"];
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
      die("ERROR:Access Denied DB");
    
    if(!$scon) {
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
      $sql->bindValue(1,$scon_pipename); 
      $sql->execute();
      $scon = $sql->fetchAll();
    } 
    if(count($scon)!=1) {
      die("Error:not found"); 
    }
    else {
      $m = $scon[0]['messageout'];
      if($scon[0]['clientreadmode']=="FOR") {
        $mo = ""; 
        $qry=$conn->prepare("UPDATE serialconnections SET date=?, messageout=? WHERE pipename=?");
        $qry->bindValue(1,$timestamp); 
        $qry->bindValue(2,$mo); 
        $qry->bindValue(3,$scon_pipename);
        $result = $qry->execute();
      }
    }
}
catch(Exception $e){
    die(print_r($e));
}
echo "ok:".$m;
?>
