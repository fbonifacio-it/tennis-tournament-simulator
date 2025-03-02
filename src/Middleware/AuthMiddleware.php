<?php

namespace App\Middleware;

use App\Repository\UserRepository;
use App\Security\TokenService;

class AuthMiddleware
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->tokenService = new TokenService();
    }


    /**
    *
    * authenticate using bearer token sent by client on the header
    *
    */
    public function authenticate()
    {
     
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized"]);
            exit;
        }


        list(, $token) = explode(' ', $headers['Authorization'], 2);

        $user = $this->userRepository->findByToken($this->tokenService->decryptToken($token));

        if (!$user || strtotime($user['token_expires_at']) < time()) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid or expired token"]);
            exit;
        }

        return $user;
    }
}
