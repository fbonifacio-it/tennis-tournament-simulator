<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\TokenService;

class AuthController
{
    private UserRepository $userRepository;
    private TokenService $tokenService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->tokenService = new TokenService();
    }

    /**
    *
    * register user
    *
    */
    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing email or password"]);
            return;
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(["error" => "Email already registered"]);
            return;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->userRepository->createUser($data['email'], $hashedPassword);

        http_response_code(201);
        echo json_encode(["message" => "User registered successfully"]);
    }

    /**
    *
    * login registered user
    *
    */
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing email or password"]);
            return;
        }

        $user = $this->userRepository->findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid credentials"]);
            return;
        }

        $accessToken = $this->tokenService->generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->userRepository->updateToken($user['id'], $accessToken, $expiresAt);

        http_response_code(200);
        echo json_encode([
            "access_token" => $this->tokenService->encryptToken($accessToken),
            "expires_at" => $expiresAt
        ]);
    }

    /**
    *
    * logout registered user
    *
    */
    public function logout()
    {
        $headers = getallheaders();
        list(, $token) = explode(' ', $headers['Authorization'], 2);

        $user = $this->userRepository->findByToken($this->tokenService->decryptToken($token));
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        $this->userRepository->invalidateToken($user['id']);

        http_response_code(200);
        echo json_encode(["message" => "Logged out successfully"]);
    }




}
