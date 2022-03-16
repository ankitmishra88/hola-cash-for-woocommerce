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

    public function getEndpoint($name){
        if($this->is_test){
            switch($name){
                case 'create_order':
                    return 'https://sandbox.api.holacash.mx/v2/order';
                default:
                    return '';
            }
        }
        else{
            switch($name){
                case 'create_order':
                    return 'https://live.api.holacash.mx/v2/order';
                default:
                    return '';
            }
        }
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