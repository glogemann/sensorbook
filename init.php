<?php
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
