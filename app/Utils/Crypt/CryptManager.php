<?php


namespace App\Utils\Crypt;


class CryptManager
{

    /**
     * @var string
     */
    private string $encrypt_method;
    /**
     * @var string
     */
    private string $secret_key;
    /**
     * @var string
     */
    private string $encrypt_iv;
    /**
     * @var string
     */
    private string $alg;

    public function __construct()
    {
        $this->encrypt_method = env('ENCRYPT_METHOD');
        $this->secret_key = env('ENCRYPT_SECRET_KEY');
        $this->encrypt_iv = env('ENCRYPT_IV');
        $this->alg = env('ENCRYPT_ALG');
    }

    public function encrypt($payload)
    {
        $key = hash($this->alg, $this->secret_key);
        $iv = $this->encrypt_iv;
        $iv = substr(hash($this->alg, $iv), 0, 16);
        return base64_encode(openssl_encrypt($payload, $this->encrypt_method, $key, 0, $iv));
    }

    public function decrypt($value)
    {
        $key = hash($this->alg, $this->secret_key);
        $iv = $this->encrypt_iv;
        $iv = substr(hash($this->alg, $iv), 0, 16);
        return openssl_decrypt(base64_decode($value), $this->encrypt_method, $key, 0, $iv);
    }

    function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'xxxxxxxxxxxxxxxxxxxxxxxx';
        $secret_iv = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
}
