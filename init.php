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
echo "INIT Serial Hub (c) Gunter Logemann";

require_once('config.php'); 

try{
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $sql = "CREATE TABLE serialconnections(
    id INT NOT NULL IDENTITY(1,1) 
    PRIMARY KEY(id),
    pipename VARCHAR(40),
    masterkey VARCHAR(40),
    clientkey VARCHAR(40),
    clientpermissioninfo VARCHAR(100), 
    mastertrigger VARCHAR(500), 
    mocount int, 
    micount int, 
    options VARCHAR(100),
    info VARCHAR(1000),
	  messagein VARCHAR(1000),
	  messageout VARCHAR(1000),
    clientmode VARCHAR(10), 
    clientreadmode VARCHAR(10),
    clientwritemode VARCHAR(10),
    masterreadmode VARCHAR(10),
    masterwritemode VARCHAR(10),
    created VARCHAR(20),
    date VARCHAR(20))";
    
    $conn->query($sql);
}
catch(Exception $e){
    die(print_r($e));
}
echo "<h3>Table created.</h3>";

?>
