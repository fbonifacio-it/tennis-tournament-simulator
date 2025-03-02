<?php


namespace App\Repository;

use App\Infrastructure\Database;
use PDO;

class UserRepository {

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
    *
    * delete autentication token from the db
    *
    */
	public function invalidateToken($userId)
	{
	    $stmt = $this->db->prepare("UPDATE users SET access_token = NULL, token_expires_at = NULL WHERE id = ?");
	    $stmt->execute([$userId]);
	}

    /**
    *
    * update autentication token from the db
    *
    */
	public function updateToken($userId, $accessToken, $expiresAt)
	{
	    $stmt = $this->db->prepare("UPDATE users SET access_token = ?, token_expires_at = ? WHERE id = ?");
	    $stmt->execute([$accessToken, $expiresAt, $userId]);
	}

    /**
    *
    * fetch user by email
    *
    */
	public function findByEmail($email)
	{
	    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
	    $stmt->execute([$email]);
	    return $stmt->fetch();
	}

    /**
    *
    * insert new user and return array or false
    *
    */
	public function createUser($email, $hashed_password): bool
    {
        try {
            $sql = "INSERT INTO users (password, email) VALUES (:password, :email)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'password' => $hashed_password,
                'email' => $email
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
    *
    * fetch user by autentication token
    *
    */
    public function findByToken($token) {
	    $stmt = $this->db->prepare("SELECT * FROM users WHERE access_token = ?");
	    $stmt->execute([$token]);
	    return $stmt->fetch();
    }


}