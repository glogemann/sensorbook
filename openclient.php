<?php
require_once('clientconfig.php'); 

try{
    // check pipename 
    $scon_pipename = $_GET["p"];
    if(!$scon_pipename) {
      echo "ERROR: no pipename";
      die(); 
    }

    // clientkey 
    $scon_clientkey = $_GET["c"];
    //if(!$scon_clientkey) {
    //  echo "ERROR: no clientkey";
    //  die(); 
    //}
    
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
	  $timestamp = time(); 
    if(CheckPermission($conn, $scon_pipename, $scon_clientkey)!=null) {
      echo "OK:".$timestamp;
    }
    else {
      echo "ERROR:Access Denied";
    }
}
catch(Exception $e){
    echo $e; 
    die();
}
?>