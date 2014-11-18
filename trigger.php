<?php
require_once('clientconfig.php'); 
require_once('windowspush.php');

$scon_pipename = $_GET["p"];
$scon_masterkey = $_GET["m"]; 
if(!$scon_pipename) {
  die("ERROR:no identifier"); 
}

$scon_clientkey = $_GET["c"];
$scon_clientname = $_GET["cn"];

// TODO: prove clientpermissions
$uri = "https://db3.notify.windows.com/?token=AgYAAABvz%2b9viFJHv4TAULOOLnw6eDcRjS%2f5PUggm%2ffekvCbcjhobpwT3mAnw6KStzBhSfr2AfCvkkwAcJEqK85SF3HsqcJEMHmOpxYNPoLqVGxujqdPLKbVLq7fv4ceIs%2bXMjo%3d";                                                                       


$timestamp = time(); 

$mt = null; 

try {
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $scon = CheckPermission($conn, $scon_pipename, $scon_clientkey);
    if($scon==null) 
      die("ERROR:Access Denied");
    
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
      $mt = $scon[0]['mastertrigger'];
      echo "OK"; 
    }
}
catch(Exception $e){
  die(print_r($e));
}


$sid = "ms-app://s-1-15-2-585107658-2024984359-2558659073-3268048767-1727727466-3782045473-674920604";
$sec = "XE0exB+whMxj6dhgxoR/csV+rS3vG4G5";

$notif=new WPN(urlencode($sid),urlencode($sec));
$msg = $notif->build_toast_xml("hallo","http://www.pccon.de");
//$notif->post_toast($uri,$msg); 
//$notif->post_toast($mt,$msg); 
$notif->post_raw($mt,$msg); 

echo "OK:".$timestamp; 
?>
