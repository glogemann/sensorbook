<?php
class WPNTypesEnum{       
    const Toast = 'wns/toast';
    const Badge = 'wns/badge';
    const Tile  = 'wns/tile';
    const Raw   = 'wns/raw';
}                         

class WPNResponse{
    public $message = '';
    public $error = false;
    public $httpCode = '';
    
    function __construct($message, $httpCode, $error = false){
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->error = $error;
    }
}

class WPN{            
    private $access_token = '';
    private $sid = '';
    private $secret = '';
         
    function __construct($sid, $secret){
        $this->sid = $sid;
        $this->secret = $secret;
    }
    
    private function get_access_token(){
        if($this->access_token != ''){
            return;
        }

		//echo "Get Access Token..";

        $str = "grant_type=client_credentials&client_id=$this->sid&client_secret=$this->secret&scope=notify.windows.com";
        $url = "https://login.live.com/accesstoken.srf";
		//echo $str;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$str");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);  
		//print_r($output);                     

        $output = json_decode($output);

        if(isset($output->error)){
            throw new Exception($output->error_description);
        }

        $this->access_token = $output->access_token; 
    }

    public function build_tile_xml($title, $img){
        return '<?xml version="1.0" encoding="utf-16"?>'.
        '<tile>'.
          '<visual lang="en-US">'.
            '<binding template="TileWideImageAndText01">'.
              '<image id="1" src="'.$img.'"/>'.
              '<text id="1">'.$title.'</text>'.
            '</binding>'.
          '</visual>'.
        '</tile>';
    }

	public function build_toast_xml($title, $img){
        return '<?xml version="1.0" encoding="utf-16"?>'.
		'<toast>'.
			'<visual lang="en-US">'.
				'<binding template="ToastText01">'.
					'<text id="1">ToastText01 - a single line of small text, wrapped as necessary across up to three lines on screen.</text>'.
				'</binding>'.
			'</visual>'.
		'</toast>';
    }

    public function post_tile($uri, $xml_data, $type = WPNTypesEnum::Tile, $tileTag = ''){
        if($this->access_token == ''){
            $this->get_access_token();
        }
 
        $headers = array('Content-Type: text/xml', "Content-Length: " . strlen($xml_data), "X-WNS-Type: $type", "Authorization: Bearer $this->access_token");
        if($tileTag != ''){
            array_push($headers, "X-WNS-Tag: $tileTag");
        }
		return $this->push($headers,$xml_data,$uri);
	}

    public function post_toast($uri, $xml_data, $type = WPNTypesEnum::Toast){
        if($this->access_token == ''){
            $this->get_access_token();
        }
 
        $headers = array('Content-Type: text/xml', "Content-Length: " . strlen($xml_data), "X-WNS-Type: $type", "Authorization: Bearer $this->access_token");
		return $this->push($headers,$xml_data,$uri);
	}
    
    public function post_raw($uri, $xml_data, $type = WPNTypesEnum::Raw){
        if($this->access_token == ''){
            $this->get_access_token();
        }
        $xml_data = "Hello World";  
        $headers = array('Content-Type: application/octet-stream', "Content-Length: " . strlen($xml_data), "X-WNS-Type: $type", "Authorization: Bearer $this->access_token");
		return $this->push($headers,$xml_data,$uri);
	}

	
	public function push($headers,$xml_data,$uri) {
		//echo "\n\rHeader:";
		//print_r($headers);
		//echo "\n\rDATA:";
		//print_r($xml_data);
        $ch = curl_init($uri);
        # Tiles: http://msdn.microsoft.com/en-us/library/windows/apps/xaml/hh868263.aspx
        # http://msdn.microsoft.com/en-us/library/windows/apps/hh465435.aspx
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
        $output = curl_exec($ch);
		//echo "\n\rOutput:";
		//print_r($output);
		
        $response = curl_getinfo( $ch );
		//echo "Response:";
		//print_r($response);
        
		curl_close($ch);
		
        $code = $response['http_code'];


        if($code == 200){
			echo "\n\rSuccess\n\r";
            return new WPNResponse('Successfully sent message', $code);
        }
        else if($code == 401){
			echo "\n\rERROR!\n\r";
            $this->access_token = '';
			return new WPNResponse('Error', $code, true);
            //return $this->post_tile($uri, $xml_data, $type, $tileTag);
        }
        else if($code == 410 || $code == 404){
			echo "\n\rERROR!\n\r";
            return new WPNResponse('Expired or invalid URI', $code, true);
        }
        else{
            echo "\n\rUnknown".$code."\n\r";
            return new WPNResponse('Unknown error while sending message', $code, true);
        } 

    }
	
}
?>
