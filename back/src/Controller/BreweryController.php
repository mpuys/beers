<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Brewery;

use App\Services\JwtAuth;
use App\Services\BreweryService;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;


class BreweryController extends AbstractController
{
    public $brewService;

    public function __construct(BreweryService $brewService)
    {
        $this->brewService = $brewService;
    }

    private function jsonResp($data)
    {
        // $encoder = new JsonEncoder();
        // $defaultContext = [
        //     AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
        //         return ['handled_id' => $object->getId(), 'handled_name' => $object->getName()];
        //     },
        //     AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
        //     AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => 0
        // ];
        // $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        // $serializer = new Serializer([$normalizer], [$encoder]);
        // $json = $serializer->serialize($data, 'json');




//         $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

//         // all callback parameters are optional (you can omit the ones you don't use)
//         $maxDepthHandler = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
//             return '/id/'.$innerObject->id;
//         };

//         $defaultContext = [
//             AbstractObjectNormalizer::MAX_DEPTH_HANDLER => $maxDepthHandler
//         ];
//         $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext);

//         $serializer = new Serializer([$normalizer]);

//         $json = $serializer->normalize($data, null, [
//             AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
//             AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => 2
//         ]);



// dump($json);exit;


        $json = $this->get('serializer')->serialize($data, 'json');

        $response = new Response;
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function index()
    {
        $brewery = $this->getDoctrine()->getRepository(Brewery::class)->findBy([
            'country' => 6
        ]);

        $brewery = $this->getDoctrine()->getRepository(Brewery::class)->findAll();

        return $this->jsonResp($brewery);
    }

    public function newBrewery(Request $request, JwtAuth $jwtAuth, $id = null)
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
                    $data = $this->brewService->new($params);
                } else {
                    $data = $this->brewService->edit($params, $id);
                }
            }
        }

        return $this->jsonResp($data);
    }

    public function list(Request $request, JwtAuth $jwtAuth, PaginatorInterface $paginator)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong'
        ];

        // $token = $request->headers->get('Authorization', null);
        // $auth_check = $jwtAuth->checkToken($token);

        // if ($auth_check) {
            $em = $this->getDoctrine()->getManager();

            $dql = "SELECT b FROM App\Entity\Brewery b ORDER BY b.id ASC";
            $query = $em->createQuery($dql);

            $page = $request->query->getInt('page', 1);
            $items_per_page = 10;

            $pagination = $paginator->paginate($query, $page, $items_per_page);
            $total = $pagination->getTotalItemCount();

            $data = [
                'status' => 'success',
                'code' => 200,
                'total_items' => $total,
                'page' => $page,
                'items_per_page' => $items_per_page,
                'total_pages' => ceil($total/$items_per_page),
                'breweries_in_page' => $pagination,
                'all_breweries' => $this->getDoctrine()->getRepository(Brewery::class)->findAll()
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
            $brewery = $doctrine->getRepository(Brewery::class)->find($id);

            if (is_object($brewery)) {
                // $em->remove($brewery);
                // $em->flush();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'brewery deleted',
                    'brewery' => $brewery
                ];
            }
        }

        return $this->jsonResp($data);
    }

    public function brewery(Request $request, JwtAuth $jwtAuth, $id = null)
    {
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong'
        ];

        // $token = $request->headers->get('Authorization', null);
        // $auth_check = $jwtAuth->checkToken($token);

        // if ($auth_check && !empty($id)) {

            $brewery = $this->getDoctrine()->getRepository(Brewery::class)->find($id);

            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'could not find brewery'
            ];

            if ($brewery && is_object($brewery)) {
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'brewery' => $brewery
                ];
            }

        // }

        return $this->jsonResp($data);
    }
}
