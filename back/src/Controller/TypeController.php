<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Type;

use App\Services\JwtAuth;
use App\Services\TypeService;

class TypeController extends AbstractController
{
    public $typeService;

    public function __construct(TypeService $typeService)
    {
        $this->typeService = $typeService;
    }

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
        $this->typeService->newType(1);

        // return $this->jsonResp($type);
    }

    public function newType(Request $request, JwtAuth $jwtAuth, $id = null)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong, try again later'
        ];

        $token = $request->headers->get('Authorization', null);
        $auth_check = $jwtAuth->checkToken($token);

        if ($auth_check) {
            $json = $request->get('json', null);

            if ($json != null) {
                $params = json_decode($json);

                if ($id == null) {
                    $data = $this->typeService->new($params);
                } else {
                    $data = $this->typeService->edit($params, $id);
                }
            }
        }

        return $this->jsonResp($data);
    }

    public function list(Request $request, JwtAuth $jwtAuth)
    {
        $types = $this->getDoctrine()->getRepository(Type::class)->findAll();

        $data = [
            'status' => 'success',
            'code' => 200,
            'types' => $types
        ];

        return $this->jsonResp($data);
    }

    public function delete(Request $request, JwtAuth $jwtAuth, $id)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong, try again later'
        ];

        $token = $request->headers->get('Authorization', null);
        $auth_check = $jwtAuth->checkToken($token);

        if ($auth_check) {
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();
            $type = $doctrine->getRepository(Type::class)->find($id);

            if (is_object($type)) {
                // $em->remove($type);
                // $em->flush();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'type deleted',
                    'type' => $type
                ];
            }
        }

        return $this->jsonResp($data);
    }
}
