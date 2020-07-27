<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Country;

use App\Services\JwtAuth;
use App\Services\CountryService;


class CountryController extends AbstractController
{
    public $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    private function jsonResp($data)
    {
        $json = $this->get('serializer')->serialize($data, 'json');

        $response = new Response;
        $response->setContent($json);
        $response->headers->set('Content-Country', 'application/json');

        return $response;
    }

    public function index()
    {
        $country = $this->getDoctrine()->getRepository(Country::class)->findAll();

        return $this->jsonResp($country);
    }

    public function newCountry(Request $request, JwtAuth $jwtAuth, $id = null)
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
                    $data = $this->countryService->new($params);
                } else {
                    $data = $this->countryService->edit($params, $id);
                }
            }
        }

        return $this->jsonResp($data);
    }

    public function list(Request $request, JwtAuth $jwtAuth)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong, try again later'
        ];

        // $token = $request->headers->get('Authorization', null);
        // $auth_check = $jwtAuth->checkToken($token);

        // if ($auth_check) {
            $countries = $this->getDoctrine()->getRepository(Country::class)->findAll();

            $data = [
                'status' => 'success',
                'code' => 200,
                'countries' => $countries
            ];
        // }

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
            $country = $doctrine->getRepository(Country::class)->find($id);

            if (is_object($country)) {
                $em->remove($country);
                $em->flush();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'country deleted',
                    'country' => $country
                ];
            }
        }

        return $this->jsonResp($data);
    }
}
