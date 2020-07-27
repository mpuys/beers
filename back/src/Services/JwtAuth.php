<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth
{
	public $manager;
	public $key;

	public function __construct($manager)
	{
		$this->manager = $manager;
		$this->key = 'ola k ase';
	}

	public function login($username, $password, $get_token = null)
	{
		$user = $this->manager->getRepository(User::class)->findOneBy([
			'username' => $username,
			'password' => $password
		]);

		$login = false;
		if (is_object($user)) {
			$login = true;
		}

		$data = [
			'status' => 'error',
			'message' => 'login failed'
		];

		if ($login) {
			$token = [
				'sub' => $user->getId(),
				'username' => $user->getUsername(),
				'iat' => time(),
				'exp' => time() + (365*24*60*60)
			];

			$jwt = JWT::encode($token, $this->key, 'HS256');
			$decoded = JWT::decode($jwt, $this->key, ['HS256']);
			$data = $decoded;

			if ($get_token) {
				$data = $jwt;
			}
		}

		return $data;
	}

	public function checkToken($token, $identity = false)
	{
		$auth = false;

		try {
			$decoded = JWT::decode($token, $this->key, ['HS256']);
		} catch (\UnexpectedValueException $e) {
		} catch (\DomainException $e) {
		}

		if (isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
			$auth = true;
		}

		if ($identity != false) {
			return $decoded;
		}

		return $auth;
	}
}