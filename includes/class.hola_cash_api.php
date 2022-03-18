<?php
/**
 * Class for communication with Hola Cash API
 */

 namespace HOLA_WC;

 defined('ABSPATH')||die('No Script Kiddies Please');

 class HOLA_WC_API{
    
    private static $public_api_key;
    private static $private_api_key;
    private static $is_test;

    public function __construct(){
        // For now let's hardcode value to test mode;
        $hola_payment_method=new HOLA_CASH_WC_GATEWAY();
        $this->public_api_key=$hola_payment_method->public_api_key;
        $this->private_api_key=$hola_payment_method->private_api_key;
        $this->is_test=$hola_payment_method->test_mode;
        $this->webhook_key=$hola_payment_method->webhook_key;

    }
	 
	public function log($text){
			$uploadsDir=wp_upload_dir();
			$logs_dir=untrailingslashit($uploadsDir['basedir']).'/hola-logs';
			if (!file_exists($welcome_dir)&&!is_dir($logs_dir)) {
				mkdir($logs_dir, 0777, true);
			}
			$log_file=$logs_dir."/".date('Y-m-d').".txt";
			
			$f=fopen($log_file,'a');
			
			$log_text=date('h:i:s A ').$text."\n";
			fwrite($f,$log_text);
			fclose($f);
	}


    public function getSingleHeaderValue($key){
        return $_SERVER["HTTP_{$key}"];
    }
	 

	public function getRequestHeaders() {
		$headers = array();
        print_r($_SERVER);
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$headers[$header] = $value;
		}
		return $headers;
	}

    function is_valid_request(){
        $this->log("Start validating request");

        $current_timestamp=time();

        if($_SERVER['REQUEST_METHOD']!='POST'){
            return new \WP_Error('unsupported_request_method',"Only POST request is supported by this endpoint");
        }
        $header_sign=$this->getSingleHeaderValue('HOLACASH_SIGN');
        if(empty($header_sign)){
            return new \WP_Error('empty_sign',__("Request must need a signature",'hola-cash-wc'));
        }

        $this->log("Signature received {$header_sign}");
        $sign_values=explode(',',$header_sign);
        $payload = @file_get_contents('php://input');
        $timestamp=$sign_values[0];
        $signature=$sign_values[1];

        $delayed_time=$current_timestamp-$timestamp;

        if($delayed_time>0&&$delayed_time>3600){
            //Let's take one hour for now
            return new \WP_Error('old_timestamp','Old timestamp');
        }

        $this->log("payload: {$payload} timestamp: {$timestamp} signature: {$signature}");


        if(empty($signature)||empty($timestamp)){
            return new \WP_Error('invalid_signature_string',__("Request contains an invalid signature string",'hola-cash-wc'));
        }

        $validating_string="{$timestamp}.{$payload}";

        $this->log("String to be converted {$validating_string}");

        $expected_signature= hash_hmac('sha256', $validating_string, $this->webhook_key);
        $this->log("Expected signatue in header {$expected_signature} using key {$this->webhook_key}");
        if($expected_signature!=$signature){
            return new \WP_Error('signature_mismatch',__("Sorry couldn't verify the signature"));
        }

        return true;

    }
	 
	public function process_webhook(){
		$this->log("<---Webhook processing started---->");
        $is_valid=$this->is_valid_request();
        if(!is_wp_error($is_valid)){
            $this->log('Request validated');
            $body=json_decode(@file_get_contents('php://input'),true);
            $event_type=sanitize_text_field($body['event_type']);
            $event_id=$body['payload']['id'];
            switch($event_type){
                case 'charge.succeeded':
                    $this->log("charge.succeeded response received with id {$event_id}");
                    break;
                case 'charge.pending':
                    $this->log("charge.pending response received with id {$event_id}");
                    break;
                case 'charge.failed':
                    $this->log("charge.failed response received with id {$event_id}");
                    break;
                case 'charge.cancelled':
                    $this->log("charge.cancelled response received with id {$event_id}");
                    break;
                case 'capture.succeeded':
                    $this->log("capture.succeeded response received with id {$event_id}");
                    break;
                case 'capture.failed':
                    $this->log("capture.failed response received with id {$event_id}");
                    break;
                case 'refund.succeeded':
                    $this->log("refund.succeeded response received with id {$event_id}");
                    break;
                case 'refund.failed':
                    $this->log("refund.failed response received with id {$event_id}");
                    break;
                default:
                    $this->log("Event type received is {$event_type}, we are not listening for this type of event with id {$event_id}");
            }

        }
        else{
            $this->log($is_valid->get_error_message());
        }
        
        $this->log("<----Webhook process ends---->");
		
	}

    public function getBaseUrl(){
        if($this->is_test){
            return 'https://sandbox.api.holacash.mx';
        }
        else{
            return 'https://live.api.holacash.mx';
        }
    }

    public function getEndpoint($name){
        
        $base_url=$this->getBaseUrl();
        switch($name){
            case 'create_order':
                return "{$base_url}/v2/order";
            case 'merchant_checkout_widget_config':
                return "{$base_url}/v2/merchant/setting/checkout-widget";
            default:
                return '';
        }

        
    }

    public function getTransactionConfig(){
        $url=$this->getEndpoint('merchant_checkout_widget_config');
        $headers=array(
            'X-Api-Client-Key'=>$this->private_api_key
        );
        $response=wp_remote_get($url,array(
            'headers'=>$headers
        ));
        if(is_wp_error($response)){
            return $response;
        }
    }

    public function charge(){
        
    }

    public function getIPAddress(){
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = '0.0.0.0';
        return $ipaddress;
    }



 }

?>