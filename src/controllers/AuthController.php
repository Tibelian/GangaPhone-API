<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Tibelian\GangaPhoneApi\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

/**
 * @see https://jwt.io/
 */
class AuthController {
    
    public function authHeader():void 
    {
        // check if the auth header exists
        if (!isset($_SERVER['HTTP_AUTHORIZATION']))
            $this->abort('miss authorization header');

        // first word should be Bearer and second one is the JWT
        $reqJWT = explode(" ", $_SERVER['HTTP_AUTHORIZATION'])[1]; 

        // validate the token
        if ($this->isValid($reqJWT) == false) 
            $this->abort();
    }

    public function isValid(string $jwt):bool 
    {
        // obtain the real auth data
        $access = Config::get('auth');

        // generate the private key
        $key = new Key($access['secret'], $access['algorithm']);

        try {

            // unhash the public key using the private key
            $decoded = JWT::decode($jwt, $key);

            // check if the real token is same as the received token
            return ($decoded->token == $access['token']);
            
        } catch(SignatureInvalidException $e) {
            $this->abort($e->getMessage());
        }

        // unreacheable
        return false;
    }

    private function abort(string $error = 'invalid token'):void 
    {
        header('HTTP/1.1 401 UNAUTHORIZED');
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'unauthorized',
            'error' => $error
        ]);
        exit; // important
    }
      

}