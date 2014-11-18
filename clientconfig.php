<?php

$server = "xxx";
$user = "xxx";
$pwd = "xxx";
$db = "xxx";

//Azure Storage connection string 
require_once 'WindowsAzure/WindowsAzure.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException; 
use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

$connectionString = "DefaultEndpointsProtocol=http;AccountName=serialhub;AccountKey=xxx";

$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

function CheckPermission($conn, $pipename, $clientkey) {
  try{
//    echo "1";
    $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
    $sql->bindValue(1,$pipename); 
    $sql->execute();
    $scon = $sql->fetchAll(); 
    if(count($scon)>0) {
        // found pipe. 
        // check if simple clientkey is used:
        //echo "2"; 
        $scon_clientkey = $scon[0]['clientkey'];
        $scon_clientmode = $scon[0]['clientmode'];
        //echo $scon_clientmode;
        $approved = "NA"; 
        if($scon_clientmode == "CRO") {
          $approved = $scon; 
        }
        else {
          if($scon_clientmode == "CRW") {
            $approved = $scon; 
          } 
          else {
            if($scon_clientkey ) {
              //echo "3";
              // if clinet key not null we simply compare the client key
              if($clientkey==null) {
                $approved=null;
              } 
              if($clientkey==$scon_clientkey) {
                $approved=$scon;
              }
            }
            else {
              //echo "4";
              // if client key null use the permissions table
              $permissiontablename = "Permission".$pipename; 
              $sql = $conn->prepare("SELECT * FROM ".$permissiontablename." WHERE clientkey = ?");
              $sql->bindValue(1,$clientkey);
              $sql->execute();
              $sconp = $sql->fetchAll();
              if(count($sconp)>0) {
                $approved = $scon; 
              }
              else {
                $approved = null;  
              }
            }
          }
        }
    }
  }
  catch(Exception $e){
    print_r($e); 
    return null;
  }
  return $approved;
}

?>
