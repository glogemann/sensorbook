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
<?php
	session_start(); 
  $pipename = $_GET['p']; 
     
  require_once('config.php');
  	// connect to database
  $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
	if($conn == null) die("Could not connect to database"); 
  $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	
	$sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
  $sql->bindValue(1,$pipename); 
  $sql->execute();
  $scon = $sql->fetchAll();
	
  if(count($scon)!=1) {
    echoresult("ERROR",$timestamp,"not found");
    die(); 
  }
  else {
    $m = $scon[0]['messageout'];
	  try {
	  	$x = json_decode($m);
      $type = $x->{'type'};
      if($type=="dwdobservationsensor") {
        $errorText="Unknown Sensor Type";
        $version = $x->{'version'};
		    $lat = $x->{'latitude'};
		    $lon = $x->{'longitude'};
      } else if ($type=="imagesensor") {
        $imageurl = $x->{'imageurl'}; 
        $errorText="";
        $version = "x";
		    $lat = "0.0";
		    $lon = "0.0";
      } else if($type=="position") {
		    $lat = $x->{'latitude'};
		    $lon = $x->{'longitude'};
      } else {
        $errorText="Unknown Sensor Type";
        $type = ""; 
        $version = "";
		    $lat = "0.0";
		    $lon = "0.0";
      }
	  }
	  catch (ServiceException $e) {
	 	    echoresult("ERROR",$timestamp,"not found");
      	die(); 
	  }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sensorbook Dashboard</title>
<link href="sensorbook.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0"></script>
<script type="text/javascript">
//-----------------------------------------------------------------------------------
// Create Map 
//-----------------------------------------------------------------------------------
var map = null; 

function onLoadPage() {
    if("<?php echo $type;?>"==="dwdobservationsensor") {
      document.getElementById("Error").style.visibility="collapse";
      document.getElementById("imageSensor").style.visibility="collapse";
      document.getElementById("imageSensorImg").style.height="0px";
      document.getElementById("positionSensor").style.visibility="collapse";
      createMap();   
    } else if("<?php echo $type;?>"==="imagesensor") {
      document.getElementById("Error").style.visibility="collapse";
      document.getElementById("dwdObservationSensor").style.visibility="collapse";
      document.getElementById("mapDiv").style.height="0px";
      document.getElementById("positionSensor").style.visibility="collapse";
    } else if("<?php echo $type;?>"==="position") {
      document.getElementById("Error").style.visibility="collapse";
      document.getElementById("dwdObservationSensor").style.visibility="collapse";
      document.getElementById("imageSensor").style.visibility="collapse";
      document.getElementById("imageSensorImg").style.height="0px";
      createMap(); 
    }
    else {  
      document.getElementById("dwdObservationSensor").style.visibility="collapse";
      document.getElementById("imageSensor").style.visibility="collapse";
      document.getElementById("mapDiv").style.height="0px";
      document.getElementById("imageSensorImg").style.height="0px";
      document.getElementById("positionSensor").style.visibility="collapse";
    }
}

function createMap()
{  
   var mapOptions =
   { 
      credentials:"AkxNNo_pPk2EkGAxUr2XQATBmzqXZF_xSC8TUIul3j4z_KmtU0105hejiiwJXxSW", 
      mapTypeId:Microsoft.Maps.MapTypeId.road, 
      center: new Microsoft.Maps.Location(<?php echo $lat."";?>,<?php echo "".$lon."";?>),
      zoom: 8
   }
   
   // create map 
   map = new Microsoft.Maps.Map(document.getElementById("mapDiv"), mapOptions ); 
   var loc = null; 
   var pin = null;
   
   // add "my location" pushpin 
   loc = new Microsoft.Maps.Location(<?php echo "".$lat."";?>,<?php echo "".$lon."";?>);
   pin = new Microsoft.Maps.Pushpin(loc, {icon: '\\Assets\\Marker.png', width: 48, height: 48}); 
   
   Microsoft.Maps.Pushpin.prototype.title = null;
   Microsoft.Maps.Pushpin.prototype.description = null;
   Microsoft.Maps.Pushpin.prototype.user = null;
   
   map.entities.push(pin);
   //map.entities.push(infobox); 
}

function Hallo() {
}

 

</script>


<style type="text/css">
.auto-style1 {
	background-color: #CCCCCC;
}
</style>
</head>
<body onload="onLoadPage();">
	<h1>Sensor: <?php echo utf8_decode ( $x->{'name'});?> (<?php echo "Type:".$type;?>) </h1>
	
  <hr />
  <stackpanel>
     <div id='positionSensor'> 
      <stackpanel>
  	   <div>
        <table style="width:100%">
          <tr>
            <td>Speed:</td>
            <td><?php echo utf8_decode ( $x->{'speed'});?> km/h</td>		
          </tr>
          <tr>
            <td>Altitude:</td>
            <td><?php echo utf8_decode ( $x->{'altitude'});?> m</td>		
          </tr>
          <tr>
            <td>Altitude Accuracy:</td>
            <td><?php echo utf8_decode ( $x->{'altitudeaccuracy'});?> m</td>		
          </tr>      
          <tr>
            <td>Heading</td>
            <td><?php echo utf8_decode ( $x->{'heading'."°"});?></td>		
          </tr>  
          <tr>
            <td>source</td>
            <td><?php echo utf8_decode ( $x->{'source'});?></td>		
          </tr>  
        </table>
       </div>
      </stackpanel>
     </div>
 
     <div id='dwdObservationSensor'> 
      <stackpanel>
  	   <div id="resultTab">
        <table style="width:100%">
          <tr>
            <td>Windgeschwindigkeit:</td>
            <td><?php echo utf8_decode ( $x->{'windspeed'});?> km/h</td>		
          </tr>
          <tr>
            <td>Luftdruck:</td>
            <td><?php echo utf8_decode ( $x->{'pressure'});?> mbar</td>		
          </tr>
          <tr>
            <td><?php echo utf8_decode ("Böen Beschreibung:") ?></td>
            <td><?php echo utf8_decode ( $x->{'gustdesc'});?></td>		
          </tr>
          <tr>
            <td><?php echo utf8_decode ("Böengeschwindigkeit:") ?></td>
            <td><?php echo utf8_decode ( $x->{'gustspeed'});?> km/h</td>		
          </tr>
          <tr>
            <td>Wetterbild:</td>
            <td><?php echo utf8_decode ( $x->{'description'});?></td>		
          </tr>
          <tr>
            <td>Temperatur:</td>
            <td><?php echo utf8_decode ( $x->{'temperature'}."°C");?></td>		
          </tr>
          <tr>
            <td><?php echo utf8_decode ("Höhe:") ?></td>
            <td><?php echo utf8_decode ( $x->{'altitude'});?> m</td>		
          </tr>        
         </table>
       </div>
      </stackpanel>
    </div>
  	<div id='mapDiv' style="position:relative; width:100%; height:400px; margin: 20px;"> </div>
    </div>
    <div id='imageSensor'>
      <img id='imageSensorImg' src="<?php echo $imageurl; ?>">  </img>
    </div>
    <div id='Error'>
      <h2>ERROR: <?php echo $errorText ?></h2>
      <h3>Message:<?php print_r($m) ?></h3>  
    </div >
  </stackpanel>

</body>
</html>