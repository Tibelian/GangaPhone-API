<?php

namespace Tibelian\GangaPhoneApi\Controller;

use Tibelian\GangaPhoneApi\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

/**
 * Manage authentication to the webservice
 * @see https://jwt.io/
 */
class AuthController {
    
    /**
     * check if header auth exists and its valid
     */
    public function authHeader():void 
    {
        // check if the auth header exists
        if (!isset($_SERVER['HTTP_AUTHORIZATION']))
            $this->abort('miss authorization header');
        if (empty($_SERVER['HTTP_AUTHORIZATION']))
            $this->abort('miss authorization header');

        // first word should be Bearer and second one is the JWT
        $req = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
        if (sizeof($req) != 2) 
            $this->abort('invalid authorization header');

        // validate the token
        if ($req[0] == "Bearer" && $this->isValid($req[1]) == false) 
            $this->abort();
    }

    /**
     * validate jwt
     */
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

    /**
     * exit program
     */
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