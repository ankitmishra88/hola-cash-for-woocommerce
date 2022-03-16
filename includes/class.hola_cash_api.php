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