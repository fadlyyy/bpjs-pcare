<?php
namespace Barqdev\BpjsPcare;

use GuzzleHttp\Client;
use LZCompressor\LZString;

class BpjsService{

    /**
     * Guzzle HTTP Client object
     * @var \GuzzleHttp\Client
     */
    private $clients;

    /**
     * Request headers
     * @var array
     */
    private $headers;

    /**
     * X-cons-id header value
     * @var int
     */
    private $cons_id;

    /**
     * X-Timestamp header value
     * @var string
     */
    private $timestamp;

    /**
     * X-Signature header value
     * @var string
     */
    private $signature;

    /**
     * X-Signature header value
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * @var string
     */
    private $base_url;

    /**
     * @var string
     */
    private $service_name;

    /**
     * @var string
     */
    private $user_key;
    
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $authorization;

    public function __construct($configurations)
    {
        $this->clients = new Client([
            'verify' => false
        ]);

        foreach ($configurations as $key => $val){
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        //set X-Timestamp, X-Signature, and finally the headers
        $this->setTimestamp()->setSignature()->setAuthorization()->setHeaders()->setKey();
    }

    protected function setHeaders()
    {
        $this->headers = [
            'X-cons-id' => $this->cons_id,
            'X-Timestamp' => $this->timestamp,
            'X-Signature' => $this->signature,
            'X-Authorization' => $this->authorization,
            'user_key' => $this->user_key
        ];
        return $this;
    }

    protected function setTimestamp()
    {
        date_default_timezone_set('UTC');
        $this->timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        return $this;
    }

    protected function setSignature()
    {
        $data = $this->cons_id;
        $signature = hash_hmac('sha256', $data ."&". $this->timestamp, $this->secret_key, true);
        $encodedSignature = base64_encode($signature);
        $this->signature = $encodedSignature;
        return $this;
    }

    protected function setKey()
    {
        $this->key = $this->headers['X-cons-id'].$this->secret_key.$this->headers['X-Timestamp'];

        return $this;
    }

    protected function setAuthorization()
    {
        $kdAplikasi = "095";

        $authorization = $this->username.":".$this->password.":".$kdAplikasi;
        $encodedAuthorization = base64_encode($authorization);
        $this->authorization = 'Basic '.$encodedAuthorization;
        return $this;
    }

    protected function get($feature)
    {
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        try {
            $response = $this->clients->request(
                'GET',
                $this->base_url . '/' . $this->service_name . '/' . $feature,
                [
                    'headers' => $this->headers,
                    'http_errors' => false
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            $response = $e;
            return $response;
        }

        return $this->stringDecrypt($this->key,$response);
    }

    protected function post($feature, $data = [], $headers = [])
    {
        $this->headers['Content-Type'] = 'text/plain';
        if(!empty($headers)){
            $this->headers = array_merge($this->headers,$headers);
        }
        try {
            $response = $this->clients->request(
                'POST',
                $this->base_url . '/' . $this->service_name . '/' . $feature,
                [
                    'headers' => $this->headers,
                    'json' => $data,
                    'http_errors' => false
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            $response = $e;
            return $response;
        }

        return $this->stringDecrypt($this->key,$response);
    }

    protected function put($feature, $data = [])
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        try {
            $response = $this->clients->request(
                'PUT',
                $this->base_url . '/' . $this->service_name . '/' . $feature,
                [
                    'headers' => $this->headers,
                    'json' => $data,
                    'http_errors' => false
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            $response = $e;
        return $response;
        }

        return $this->stringDecrypt($this->key,$response);
    }


    protected function delete($feature, $data = [])
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        try {
            $response = $this->clients->request(
                'DELETE',
                $this->base_url . '/' . $this->service_name . '/' . $feature,
                [
                    'headers' => $this->headers,
                    'json' => $data,
                    'http_errors' => false
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            $response = $e;
        return $response;
        }

        return $this->stringDecrypt($this->key,$response);
    }

    // function decrypt
    protected function stringDecrypt($key, $string){
        
        $string = json_decode($string, true);
        
        $encrypt_method = 'AES-256-CBC';

        // hash
        $key_hash = hex2bin(hash('sha256', $key));
  
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        if($string['response'] && !is_array($string['response'])){ 
            $output = openssl_decrypt(base64_decode($string['response']), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv); 
            $output = LZString::decompressFromEncodedURIComponent($output);
            
            $string['response'] = $output; 
        }
        return json_encode($string);
    }

}