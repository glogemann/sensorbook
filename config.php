<?php

$server = "tcp:xxxx.database.windows.net,1433";
$user = "xxx@xxx";
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

// configure your general accesstoken 
$accesstoken = "123"; 

$atoken  = $_GET["t"]; 
if($accesstoken != null) {
  if($accesstoken == $atoken) {
  }
  else  {
    if($_SESSION['accesstoken'] == $accesstoken) {
      
    }
    else {
      echoresult("ERROR",$timestamp,"Accces Denied1");
      die();
    }
  } 
}
function echoresult($result, $timestamp, $info) {
  if($_GET["j"]!=null) {
    echo "{\"result\": \"".$result."\", \"timestamp\":\"".$timestamp."\", \"info\": \"".$info."\"}";
  }
  else {
    echo $result.":".$info;
  }
}
?>