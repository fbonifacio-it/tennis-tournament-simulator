<?php

namespace App\Security;

class TokenService
{
    private string $secretKey;
    private string $cipherMethod = 'AES-256-CBC';

    public function __construct()
    {
        $this->secretKey = getenv('OAUTH_SECRET_KEY');
    }

    /**
    *
    * create base autentication token
    *
    */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32)); // 64-character secure token
    }

    /**
    *
    * encrypt base autentication token to store in the db
    *
    */
    public function encryptToken(string $token): string
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipherMethod));
        $encrypted = openssl_encrypt($token, $this->cipherMethod, $this->secretKey, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
    *
    * dencrypt autentication token stored in the db
    *
    */
    public function decryptToken(string $encryptedToken): ?string
    {
        $data = base64_decode($encryptedToken);
        $ivLength = openssl_cipher_iv_length($this->cipherMethod);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, $this->cipherMethod, $this->secretKey, 0, $iv);
    }

}
