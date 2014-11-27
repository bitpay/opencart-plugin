<?php
/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */

class Bitpay
{

    private $config = array(
        'apiKey' => null,
        'apiServer' => 'live',
        'currency' => 'BTC',
        'pluginInfo' => null,
        // 'verifyPos' => true
    );

    private $api_servers = array(
        'live' => 'https://bitpay.com/api/',
        'test' => 'https://test.bitpay.com/api/'
    );

    private $errorMessage = '';

    public function __construct($config = array()) {
        if(!$config) {
            $this->errorMessage = $this->errorString('Config Required');
            $this->errorLog();
        }
        $this->setConfig($config);
        return $this;
    }

    public function setConfig ($config = array()) {

        // Remove invalid configuration keys
        $config = $this->pruneConfig($config);

        // Validate all configurations
        $result = $this->validateAll($config);
        if($result === true){
            $this->config = array_merge($this->config, $config);
            return true;
        }else{
            $this->errorMessage = implode(' ', $result);
            $this->errorLog();
            return false;
        }
    }

    public function createInvoice ($price, $data = array()) {

        $options = array();
        $data['price'] = (string)$price;

        // Remove invalid data keys
        $data = $this->pruneData($data);

        // Validate all data
        $result = $this->validateAll($data);
        if($result !== true){
            $this->errorMessage = implode(' ', $result);
            $this->errorLog();
            return false;
        }

        // If Verify POS is enabled, hash the POS Data with the API Key as a salt, include the hash with the POS Data,
        // then revalidate it
        // if($this->config['verifyPos'] && isset($data['posData'])) {
        //     $posData = array('posData' => $data['posData']);
        //     $posData['hash'] = crypt(serialize($data['posData']), $this->config['apiKey']);
        //     $data['posData'] = json_encode($posData);
        //     $result = $this->validate('posData', $data['posData']);
        //     if($result !== true){
        //         $this->errorMessage = $result;
        //         $this->errorLog();
        //         return false;
        //     }
        // }

        if(!isset($data['currency'])) {
            $data['currency'] = $this->config['currency'];
        }

        $options['data'] = $data;
        $options['type'] = 'POST';



        if($response = $this->apiRequest('invoice', $options)){
            return $response;
        }else{
            return false;
        }

    }

    public function getInvoice($invoiceId) {
        $result = $this->validate('invoiceId', $invoiceId);
        if($result !== true){
            $this->errorMessage = $result;
            $this->errorLog();
            return false;
        }
        if($response = $this->apiRequest("invoice/$invoiceId")){
            return $response;
        }else{
            return false;
        }
    }

    public function verifyNotification() {
        $data = file_get_contents("php://input");
        if (!$data) {
            $this->errorMessage = $this->errorString('IPN Received no data');
            $this->errorLog();
            return false;
        }

        $json = json_decode($data, true);

        if (!$json) {
            $this->errorMessage = $this->errorString('IPN Received an invalid JSON Response', $data);
            $this->errorLog();
            return false;
        }

        if (!array_key_exists('id', $json)) {
            $this->errorMessage = $this->errorString('IPN didn\'t receive Invoice ID', var_export($json, true));
            $this->errorLog();
            return false;
        }

        $invoice = $this->getInvoice( $json['id'] );
        if ($invoice === false) {
            $this->errorMessage = $this->errorString('IPN couldn\'t retrieve invoice: '.$this->errorMessage);
            $this->errorLog();
            return false;
        }

        return $invoice;
    }

    public function error() {
        return $this->errorMessage;
    }

    public function validCurrency($currencyCode) {
        $currencies = array(  'USD','EUR','GBP','JPY','CAD','AUD','CNY','CHF','SEK','NZD','KRW','AED','AFN','ALL','AMD',
            'ANG','AOA','ARS','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BRL','BSD','BTN','BWP',
            'BYR','BZD','CDF','CLF','CLP','COP','CRC','CVE','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ETB','FJD','FKP',
            'GEL','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','IQD','ISK','JEP',
            'JMD','JOD','KES','KGS','KHR','KMF','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','LYD','MAD',
            'MDL','MGA','MKD','MMK','MNT','MOP','MRO','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR',
            'OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SGD',
            'SHP','SLL','SOS','SRD','STD','SVC','SYP','SZL','THB','TJS','TMT','TND','TOP','TRY','TTD','TWD','TZS','UAH',
            'UGX','UYU','UZS','VEF','VND','VUV','WST','XAF','XAG','XAU','XCD','XOF','XPF','YER','ZAR','ZMW','ZWL','BTC');
        if(in_array($currencyCode, $currencies)) {
            return true;
        }
        return false;
    }

    private function apiRequest($path = "", $options = array()) {

        if ($this->config['apiKey'] === null) {
            $this->errorMessage = $this->errorString('API Key not set');
            $this->errorLog();
            return false;
        }

        $url = $this->api_servers[$this->config['apiServer']].$path;
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->errorMessage = $this->errorString('API URL doesn\'t conform to RFC 2396', $url);
            $this->errorLog();
            return false;
        }

        $default_options = array(
            'data' => array(),
            'headers' => array(),
            'timeout' => 10,
            'type' => 'GET'
        );

        $settings = array_merge($default_options, $options);

        $auth = base64_encode($this->config['apiKey']);
        $settings['headers']['Authorization'] = "Basic $auth";

        $curl = curl_init($url);

        $length = 0;

        if ($settings['type'] === 'POST')
        {
            $data = json_encode($settings['data']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $length = strlen($data);
        }

        $settings['headers']['Content-Type'] = 'application/json';
        $settings['headers']['Content-Length'] = (string)$length;

        if(isset($this->config['pluginInfo'])) {
            $settings['headers']['X-BitPay-Plugin-Info'] = $this->config['pluginInfo'];
        }

        $header = array();
        foreach ($settings['headers'] as $key => $value) {
            $header[] = "$key: $value";
        }

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, $settings['timeout']);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // verify certificate !!!!!CHANGE BACK TO 1
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        $responseString = curl_exec($curl);

        if($responseString === false)
        {
            $this->errorMessage = $this->errorString(curl_error($curl));
            $this->errorLog();
            return false;
        }
        else
        {
            $response = json_decode($responseString, true);
            if (!$response)
            {
                $this->errorMessage = $this->errorString('Invalid JSON Response', $responseString);
                $this->errorLog();
                return false;
            }
            if(isset($response['error']))
            {
                $headers = curl_getinfo($curl, CURLINFO_HEADER_OUT);
                if(!is_string($response['error'])) {
                    $response['error'] = var_export($response['error'], true);
                }
                $this->errorMessage = $this->errorString("API Error ($url)", $response['error']);
                $this->errorLog();
                return false;
            }
        }
        curl_close($curl);

        return $response;
    }

    private function pruneConfig($config) {
        $valid_configs = array('apiKey', 'apiServer', 'currency', 'pluginInfo', 'verifyPos');
        $clean = array();
        foreach($config as $key => $value) {
            if(in_array($key, $valid_configs)){
                $clean[$key] = $value;
            }
        }
        return $clean;
    }

    private function pruneData($data) {
        $valid_data = array('buyerCity','buyerState','buyerZip','buyerCountry','buyerEmail','buyerPhone','currency',
            'fullNotifications','itemCode','itemDesc','notificationEmail','notificationURL','orderID','physical',
            'posData','price','redirectURL','transactionSpeed');
        $clean = array();
        foreach($data as $key => $value) {
            if(in_array($key, $valid_data)){
                $clean[$key] = $value;
            }
        }
        return $clean;
    }

    private function validate($key, $value) {
        $error = false;
        switch ($key) {
            case 'apiKey':
                if(!$value){
                    $error = $this->errorString('API Key is required');
                }elseif(!is_string($value)) {
                    $error = $this->errorString('API Key needs to be a string. Received '.gettype($value));
                }
                break;
            case 'apiServer':
                if(!in_array($value, array('live', 'test'))){
                    $error = $this->errorString('Invalid API Server', $value);
                }
                break;
            case 'buyerCity':
            case 'buyerState':
            case 'buyerZip':
            case 'buyerCountry':
            case 'buyerEmail':
            case 'buyerPhone':
                $prettyName = "Buyer ".substr($key, 5);
                if(!is_string($value)) {
                    $error = $this->errorString($prettyName.' needs to be a string. Received '.gettype($value));
                }elseif(strlen($value) > 100){
                    $error = $this->errorString($prettyName.' exceeds 100 character limit');
                }
                break;
            case 'currency':
                if(!$this->validCurrency($value)){
                    $error = $this->errorString('Invalid Currency Code', $value);
                }
                break;
            case 'fullNotifications':
                if(!is_bool($value)){
                    $error = $this->errorString('Full Notifications needs to be a boolean. Received '.gettype($value));
                }
                break;
            case 'invoiceId':
                if(!is_string($value)) {
                    $error = $this->errorString('Invoice ID needs to be a string. Received '.gettype($value));
                }
                break;
            case 'itemCode':
                if(!is_string($value)) {
                    $error = $this->errorString('Item Code needs to be a string. Received '.gettype($value));
                }elseif(strlen($value) > 100){
                    $error = $this->errorString('Item Code exceeds 100 character limit');
                }
                break;
            case 'itemDesc':
                if(!is_string($value)) {
                    $error = $this->errorString('Item Description needs to be a string. Received '.gettype($value));
                }elseif(strlen($value) > 100){
                    $error = $this->errorString('Item Description exceeds 100 character limit');
                }
                break;
            case 'notificationEmail':
                if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $error = $this->errorString('Notification Email doesn\'t validate as an email address', $value);
                }
                break;
            case 'notificationURL':
                if(!filter_var($value, FILTER_VALIDATE_URL)) {
                    $error = $this->errorString('Notification URL doesn\'t conform to RFC 2396', $value);
                }
                break;
            case 'orderID':
                if(!is_string($value)) {
                    $error = $this->errorString('Order ID needs to be a string. Received '.gettype($value));
                }elseif(strlen($value) > 100){
                    $error = $this->errorString('Order ID exceeds 100 character limit');
                }
                break;
            case 'physical':
                if(!is_bool($value)){
                    $error = $this->errorString('Physical needs to be a boolean. Received '.gettype($value));
                }
                break;
            case 'pluginInfo':
                if(!is_string($value)) {
                    $error = $this->errorString('Plugin Info needs to be a string. Received '.gettype($value));
                }elseif(strlen($value) > 50){
                    $error = $this->errorString('Plugin Info exceeds 100 character limit');
                }
                break;
            case 'posData':
                if(!is_string($value)) {
                    $error = $this->errorString('POS Data needs to be a string. Received '.gettype($value));
                }elseif(strlen($value) > 100){
                    $error = $this->errorString('POS Data exceeds 100 character limit');
                }
                break;
            case 'price':
                if(!is_numeric($value)) {
                    $error = $this->errorString('Price needs to be numeric', $value);
                }
                break;
            case 'redirectURL':
                if(!filter_var($value, FILTER_VALIDATE_URL)) {
                    $error = $this->errorString('Redirect URL doesn\'t conform to RFC 2396', $value);
                }
                break;
            case 'transactionSpeed':
                if(!in_array($value, array('high', 'medium', 'low'))){
                    $error = $this->errorString('Invalid Transaction Speed', $value);
                }
                break;
            case 'verifyPos':
                if(!is_bool($value)){
                    $error = $this->errorString('Verify POS needs to be a boolean. Received '.gettype($value));
                }
                break;

        }
        if ($error) {
            return $error;
        }
        return true;
    }

    private function validateAll($array) {
        $error = array();
        foreach ($array as $key => $value) {
            $valid = $this->validate($key, $value);
            if($valid !== true) {
                $error[$key] = $valid;
            }
        }
        if ($error) {
            return $error;
        }
        return true;
    }

    private function errorString($reason, $description = null) {
        if ($description) {
            $reason .= ": $description";
        }
        return $reason;
    }

    private function errorLog() {
        error_log('[BitPay] '.$this->error());
    }

}
