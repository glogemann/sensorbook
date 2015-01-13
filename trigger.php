<?php
// The MIT License (MIT) 
 
// Copyright (c) 2014 Microsoft DX  
 
// Permission is hereby granted, free of charge, to any person obtaining a copy 
// of this software and associated documentation files (the "Software"), to deal 
// in the Software without restriction, including without limitation the rights 
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
// copies of the Software, and to permit persons to whom the Software is 
// furnished to do so, subject to the following conditions: 

// The above copyright notice and this permission notice shall be included in all 
// copies or substantial portions of the Software. 
 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
// SOFTWARE. 
require_once('clientconfig.php'); 
require_once('windowspush.php');

$scon_pipename = $_GET["p"];
$scon_masterkey = $_GET["m"]; 
if(!$scon_pipename) {
  echoresult("ERROR",$timestamp,"no identifier");
  die(); 
}

$scon_clientkey = $_GET["c"];
$scon_clientname = $_GET["cn"];

// TODO: prove clientpermissions
//$uri = "https://db3.notify.windows.com/?token=AgYAAABvz%2b9viFJHv4TAULOOLnw6eDcRjS%2f5PUggm%2ffekvCbcjhobpwT3mAnw6KStzBhSfr2AfCvkkwAcJEqK85SF3HsqcJEMHmOpxYNPoLqVGxujqdPLKbVLq7fv4ceIs%2bXMjo%3d";                                                                       


$timestamp = time(); 

$mt = null; 

try {
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    if($conn == null) 
    {
        echoresult("ERROR",$timestamp,"db error");
        die();  
    }
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $scon = CheckPermission($conn, $scon_pipename, $scon_clientkey);
    if($scon==null) {
      echoresult("ERROR", $timestamp, "Access Denied");
      die();
    }  
    
    if(!$scon) {
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");  
      $sql->bindValue(1,$scon_pipename); 
      $sql->execute();
      $scon = $sql->fetchAll();
    } 
    if(count($scon)!=1) {
      echoresult("ERROR", $timestamp, "not found");
      die();
    }
    else {
      $mt = $scon[0]['mastertrigger'];
      //echo "OK"; 
    }
}
catch(Exception $e){
  die(print_r($e));
}


// Add your App secrets here (get it from the Store) 
$sid = "ms-app://s-1-15-2-585107658-2024984359-2558659073-3268048767-1727727466-3782045473-674920604";
$sec = "XE0exB+whMxj6dhgxoR/csV+rS3vG4G5";

$notif=new WPN(urlencode($sid),urlencode($sec));
$msg = $notif->build_toast_xml("hallo","http://www.pccon.de");
//$notif->post_toast($uri,$msg); 
//$notif->post_toast($mt,$msg); 
$notif->post_raw($mt,$msg); 

echoresult("OK", $timestamp, $timestamp);
?>
