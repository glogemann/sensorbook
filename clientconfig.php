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
require_once 'configdata.php';

//Azure Storage connection string 
require_once 'WindowsAzure/WindowsAzure.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException; 
use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

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

function echoresult($result, $timestamp, $info) {
  if($_GET["j"]!=null) {
    if($_GET["r"]!=null) {
      if( $result != "OK") {
         echo "{\"result\": \"".$result."\", \"timestamp\":\"".$timestamp."\", \"info\": \"".$info."\"}";
         http_response_code(404); 
      }
    }
    else {
      echo "{\"result\": \"".$result."\", \"timestamp\":\"".$timestamp."\", \"info\": \"".$info."\"}";
    }
  }
  else {
    echo $result.":".$info;
    http_response_code(200); 
  }
}

?>
