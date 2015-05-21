<?php

class Wage_Codebaseclient_Model_Abstract {

	public function __construct(){

        $this->hostname = Mage::getStoreConfig('codebaseclient/general/host');
        $this->secure = 's';
        $this->api_user = Mage::getStoreConfig('codebaseclient/general/apiuser');
        $this->api_key = Mage::getStoreConfig('codebaseclient/general/apikey');
        $this->debug = Mage::getStoreConfigFlag("codebaseclient/general/codebaselog");;
        $this->mode = 'apikey';
        $this->url = 'http'.$this->secure.'://api3.codebasehq.com';
	}
	
	public function debugLog($log){
		if($this->debug){
			Mage::log($log,null,"wage_codebaseclient.log");
		}
	}

    public function projects() {
        $projects = $this->get('/projects');
        if($projects===false) return false;
        $xml = $this->object2array(simplexml_load_string($projects,'SimpleXMLElement',LIBXML_NOCDATA));
        return $xml['project'];
    }

    public function users() {
        $users = $this->get('/users');
        if($users === false) return false;
        $xml = $this->object2array(simplexml_load_string($users,'SimpleXMLElement',LIBXML_NOCDATA));
        return $xml['user'];
    }

    public function projectusers($permalink) {
        $projects = $this->get('/'.$permalink.'/assignments');
        if($projects===false) return false;
        $xml = $this->object2array(simplexml_load_string($projects,'SimpleXMLElement',LIBXML_NOCDATA));
        return $xml['user'];
    }

    private function request($url=null,$xml=null,$post) {
        $this->debugLog("url: ".$this->url.$url);
        $ch = curl_init($this->url.$url);

        $cert =  Mage::getBaseDir().'/js/cert/COMODORSACertificationAuthority';
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, $cert);

        if($post) {
            curl_setopt($ch, CURLOPT_POST, $post);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        }
        $headers = array(
            'Content-Type: application/xml',
            'Accept: application/xml'
        );
        try {
        if($this->mode=='apikey') {
            $headers[] = 'Authorization: Basic ' . base64_encode($this->api_user . ':'. $this->api_key);
        } else {
            curl_setopt($ch, CURLOPT_USERPWD, $this->hostname . '/'.$this->username . ':' . $this->password);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        } catch (Exception $e){
            $this->debugLog("error: ".$e->getMessage());
        }
        $this->debugLog("response: ".$output);
        if(!$output || strlen($output)==1) {
//echo "Error. ".curl_error($ch);
            return false;
        } else {
            return $output;
        }
        curl_close($ch);
    }

    private function putrequest($url=null,$xml=null) {
        $this->debugLog("url: ".$this->url.$url);
        $ch = curl_init($this->url.$url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $headers = array(
            'Content-Type: application/xml',
            'Accept: application/xml'
        );
        try {
            if($this->mode=='apikey') {
                $headers[] = 'Authorization: Basic ' . base64_encode($this->api_user . ':'. $this->api_key);
            } else {
                curl_setopt($ch, CURLOPT_USERPWD, $this->hostname . '/'.$this->username . ':' . $this->password);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
        } catch (Exception $e){
            $this->debugLog("error: ".$e->getMessage());
        }
        $this->debugLog("response: ".$output);
        if(!$output || strlen($output)==1) {
//echo "Error. ".curl_error($ch);
            return false;
        } else {
            return $output;
        }
        curl_close($ch);
    }

    public function tickets($permalink,$find,$page) {
        $params = array(
            'query' => $find,
            'page' => $page,
        );
        //$url = '/'.$permalink.'/tickets?query=sort:priority status:open';
        $url = '/'.$permalink.'/tickets?'.http_build_query($params);
        $xml = $this->object2array(simplexml_load_string($this->get($url),'SimpleXMLElement',LIBXML_NOCDATA));
        return $xml['ticket'];
    }


    public function addTicket($project,$params) {
        $xml = '<ticket>';
        foreach($params as $key=>$value) {
            $xml .= '<'.$key.'><![CDATA['.$value.']]></'.$key.'>';
        }
        $xml .= '</ticket>';
        $result = $this->post('/'.$project.'/tickets',$xml);
        $result = $this->object2array(simplexml_load_string($result,'SimpleXMLElement',LIBXML_NOCDATA));
        return $result;
    }
   
    protected function post($url=null,$xml=null) {
        return $this->request($url,$xml,1);
    }
    protected function put($url=null,$xml=null) {
        return $this->putrequest($url,$xml);
    }
    protected function get($url=null) {
        return $this->request($url,null,0);
    }

    protected function object2array($object) { return @json_decode(@json_encode($object),1); }
}

