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
/**
*Windows Phone 7 Push Notification in php by Rudy HUYN
**/
final class WindowsPhonePushDelay
{

const Immediate=0;
const In450Sec=10;
const In900Sec=20;

 private function __construct(){}
}

class WindowsPhonePushNotification
{
    private $notif_url = '';
 
    function WindowsPhonePushNotification($notif_url)
    {
        $this->notif_url = $notif_url;
    }

 
	  /**
	  * Toast notifications are system-wide notifications that do not disrupt the user workflow or require intervention to resolve. They are displayed at the top of the screen for ten seconds before disappearing. If the toast notification is tapped, the application that sent the toast notification will launch. A toast notification can be dismissed with a flick.
	  * Two text elements of a toast notification can be updated:
	  * Title. A bolded string that displays immediately after the application icon.
	  * Sub-title. A non-bolded string that displays immediately after the Title.
	  */
    public function push_toast($title, $subtitle,$delay = WindowsPhonePushDelay::Immediate, $message_id=NULL)
    {
        echo "Send Toast:";  
        $msg =	"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<wp:Notification xmlns:wp=\"WPNotification\">" .
                "<wp:Toast>" .
                "<wp:Text1>".htmlspecialchars($title)."</wp:Text1>" .
                "<wp:Text2>".htmlspecialchars($subtitle)."</wp:Text2>" .
                "</wp:Toast>" .
                "</wp:Notification>";
        return $this->push('toast',$delay+2,$message_id, $msg);
}

/**
*A Tile displays in the Start screen if the end user has pinned it. Three elements of the Tile can be updated:
*@background_url : You can use a local resource or remote resource for the background image of a Tile.
*@title : The Title must fit a single line of text and should not be wider than the actual Tile. If this value is not set, the already-existing Title will display in the Tile.
*@count. an integer value from 1 to 99. If not set in the push notification or set to any other integer value, the current Count value will continue to display.
*/
public function push_tile($background_url, $title, $count, $delay = WindowsPhonePushDelay::Immediate,$message_id=NULL)
{
$msg = 	"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
            "<wp:Notification xmlns:wp=\"WPNotification\">".
            "<wp:Tile>" .
            "<wp:BackgroundImage>".htmlspecialchars($background_url)."</wp:BackgroundImage>".
            "<wp:Count>$count</wp:Count>" .
            "<wp:Title>".htmlspecialchars($title)."</wp:Title>" .
            "</wp:Tile>" .
            "</wp:Notification>";

return $this->push('token',$delay+1, $message_id,$msg);
}


/**
* If you do not wish to update the Tile or send a toast notification, you can instead send raw information to your application using a raw notification. If your application is not currently running, the raw notification is discarded on the Microsoft Push Notification Service and is not delivered to the device. The payload of a raw notification has a maximum size of 1 KB.
*/
public function push_raw($data, $delay = WindowsPhonePushDelay::Immediate,$message_id=NULL)
{
return $this->push(NULL,$delay+3,$message_id, $data);
}


/**
*@target : type of notification
*@delay : immediate, in 450sec or in 900sec
*@message_id : The optional custom header X-MessageID uniquely identifies a notification message. If it is present, the same value is returned in the notification response. It must be a string that contains a UUID
*/
private function push($target,$delay,$message_id,$msg)
{
  echo "push:";
  echo $target;
  echo $delay;
  echo $message_id;

  $sendedheaders=  array(
  'Content-Type: text/xml',
  'Accept: application/*',
  "X-NotificationClass: $delay"
  );
  if($message_id!=NULL)
    $sendedheaders[]="X-MessageID: $message_id";
  if($target!=NULL)
    $sendedheaders[]="X-WindowsPhone-Target:$target";

  echo "1";
  $req = curl_init(); 
  if($req==null) echo "no Req"; 
  else echo "REQ"; 

  curl_setopt($req, CURLOPT_HEADER, true);
  curl_setopt($req, CURLOPT_HTTPHEADER,$sendedheaders);
  curl_setopt($req, CURLOPT_POST, true);
  curl_setopt($req, CURLOPT_POSTFIELDS, $msg);
  curl_setopt($req, CURLOPT_URL, $this->notif_url);
  curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($req, CURLOPT_SSL_VERIFYPEER, 0); 
  curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 0); 
  $response = curl_exec($req);
  $curlInfo = curl_getinfo($req);
  $ce = curl_error ($req );
  echo ":::::";
  print_r($sendedheaders);
  echo ":::::";
  echo "Error".$ce;
  echo ":::::";
  print_r($req);
  echo ":::::";
  print_r($curlInfo);
  $result=array();
  echo ":::::";
  print_r($response);
  foreach(explode("\n",$response) as $line)
  {
    echo "4";
    echo $line;
    $tab=explode(":",$line,2);
    if(count($tab)==2) {
      echo "5";
      $result[$tab[0]]=trim($tab[1]);
    }
  }
  return $result;
  }
}

?>