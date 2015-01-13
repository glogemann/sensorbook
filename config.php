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
// configure adminkey

// configure your general accesstoken 
$atoken  = $_GET["t"]; 
if($accesstoken != null) {
  if($accesstoken == $atoken) {
  }
  else  {
    if($_SESSION['accesstoken'] == $accesstoken) {
      
    }
    else {
      $admin = $_GET["a"];
      if($admin == $adminkey) { 

      }
      else {
        echoresult("ERROR",$timestamp,"Accces Denied");
        die();
      }
    }
  } 
}

function echoresult($result, $timestamp, $info) {
  if($_GET["j"]!=null) {
    if($_GET["r"]!=null) {
      if( $result != "OK") {
         echo "{\"result\": \"".$result."\", \"timestamp\":\"".$timestamp."\", \"info\": \"".$info."\"}";
         http_response_code(400); 
      }
    }
    else {
      echo "{\"result\": \"".$result."\", \"timestamp\":\"".$timestamp."\", \"info\": \"".$info."\"}";
      http_response_code(200);
    }
  }
  else {
    echo $result.":".$info;
    http_response_code(200); 
  }
}
?>