<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\Validator\Validation;
// use Symfony\Component\Validator\Constraints\Email;
use App\Services\JwtAuth;

use App\Entity\User;

class UserController extends AbstractController
{
    private function jsonResp($data)
	{
		$json = $this->get('serializer')->serialize($data, 'json');

		$response = new Response;
		$response->setContent($json);
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

    public function index()
    {
    	$user_repo = $this->getDoctrine()->getRepository(User::class);
    	$users = $user_repo->findAll();

        return $this->jsonResp($users);
    }

    public function create(Request $request)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'user not created'
        ];

        $json = $request->get('json', null);
        
        if ($json != null) {
            $params = json_decode($json, true);
            $username = $params['username'] ? $params['username'] : null;
            $password = $params['password'] ? $params['password'] : null;

            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();
            $user_repo = $doctrine->getRepository(User::class);

            if (!empty($username) && !empty($password)) {
                $isset_user = $user_repo->findBy([
                    'username' => $username
                ]);

                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'username already used'
                ];

                if (count($isset_user) == 0) {                    
                    $user = new User;

                    $user->setUsername($username);
                    $password = hash('sha256', $password);
                    $user->setPassword($password);

                    $em->persist($user);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'user has been created correctly'
                    ];
                }

            }
        }

        return $this->jsonResp($data);
    }

    public function login(Request $request, JwtAuth $jwtAuth)
    {
        $json = $request->get('json', null);
        
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'unknown user'
        ];

        if ($json != null) {
            $params = json_decode($json);

            $username = !empty($params->username) ? $params->username : null;
            $password = !empty($params->password) ? $params->password : null;
            $get_token = !empty($params->get_token) ? $params->get_token : null;

            if (!empty($username) && !empty($password)) {
                $pwd = hash('sha256', $password);

                $auth = $jwtAuth->login($username, $pwd);
                if ($get_token) {
                    $auth = $jwtAuth->login($username, $pwd, $get_token);
                }

                return new JsonResponse($auth);
            }
        }

        return $this->jsonResp($data);
    }
}
