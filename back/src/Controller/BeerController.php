<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Beer;

use App\Services\JwtAuth;
use App\Services\BeerService;
use App\Services\TypeService;
use App\Services\BreweryService;

class BeerController extends AbstractController
{
	public $beerService;

	public function __construct(BeerService $beerService, TypeService $typeService, BreweryService $breweryService)
	{
		$this->beerService = $beerService;
		$this->typeService = $typeService;
		$this->breweryService = $breweryService;
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
		// $beer = $this->getDoctrine()->getRepository(Beer::class)->findAll();

		// dump($beer);exit;

		// return $this->jsonResp($beer);
	}

	public function newBeer(Request $request, JwtAuth $jwtAuth)
	{
		$data = [
			'status' => 'error',
			'code' => 400,
			'message' => 'something went wrong, try again later'
		];

		$token = $request->headers->get('Authorization', null);
		$auth_check = $jwtAuth->checkToken($token);

		if ($auth_check) {
			$img_file = $request->files->get('img') ? $request->files->get('img') : null;
			$logo1_file = $request->files->get('newBrew1_logo') ? $request->files->get('newBrew1_logo') : null;
			$logo2_file = $request->files->get('newBrew2_logo') ? $request->files->get('newBrew2_logo') : null;
			$beer = json_decode($request->get('beer', null));

			if ($beer != null) {
				//save beer img
				if ($img_file) {
					$this->beerService->saveImgs($img_file, $beer);
				}

				if ($beer->type->id == 0){
					$type_return = $this->typeService->new($beer->type);
					$beer->type->id = $type_return['type']->getId();
					$data['message'] .= ' // type saved';
				}

				if ($beer->brewery->brewery1->id == 0 && $beer->brewery->brewery1->name != ''){
					if ($logo1_file) {
						//save logo
					}

					$brewery_return = $this->breweryService->new($beer->brewery->brewery1);
					$brewery = $brewery_return['brewery'];
					$beer->brewery->brewery1->id = $brewery->getId();
					$data['message'] .= ' // brewery1 saved';
				}

				if ($beer->brewery->brewery2 != ''){
					if ($beer->brewery->brewery2->id == 0 && $beer->brewery->brewery2->name != '') {
						if ($logo2_file) {
							//save logo
						}

						$brewery_return = $this->breweryService->new($beer->brewery->brewery2);
						$brewery = $brewery_return['brewery'];
						$beer->brewery->brewery2->id = $brewery->getId();
						$data['message'] .= ' // brewery2 saved';
}
				} else {
					$beer->brewery->brewery2 = ['id' => 0, 'name' => ''];
					$beer->brewery->brewery2 = (object)$beer->brewery->brewery2;
				}

				$data = $this->beerService->newBeer($beer);
			}
		}

		return $this->jsonResp($data);
	}

	public function list(Request $request, PaginatorInterface $paginator)
	{
		$data = [
			'status' => 'error',
			'code' => 400,
			'message' => 'something went wrong'
		];

		$em = $this->getDoctrine()->getManager();

		$json = $request->get('json', null);

		$query = $em->createQuery("SELECT b FROM App\Entity\Beer b ORDER BY b.name ASC");
		if ($json != null) {
			$filters = json_decode($json);

			switch ($filters) {
				case !empty($filters->type != null):
					$query = $em->createQuery("SELECT b FROM App\Entity\Beer b WHERE b.type = ?1 ORDER BY b.name ASC");
					$query->setParameter(1, $filters->type->id);
					break;

				case !empty($filters->brewery != null):
					$query = $em->createQuery("SELECT b FROM App\Entity\Beer b JOIN b.breweries brew WHERE brew.id = :brewery_id ORDER BY b.name ASC");
					$query->setParameter('brewery_id', $filters->brewery->id);
					break;

				case !empty($filters->country != null):
					$query = $em->createQuery("SELECT b FROM App\Entity\Beer b JOIN b.breweries brew JOIN brew.country c WHERE brew.country = ?1 ORDER BY b.name ASC");
					$query->setParameter(1, $filters->country->id);
					break;
			}
		}

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
			'beers' => $pagination
		];

		return $this->jsonResp($data);
	}

	public function beer(Request $request, $id = null)
	{
		$data = [
			'status' => 'error',
			'code' => 400,
			'message' => 'something went wrong'
		];

		if (!empty($id)) {
			$beer = $this->getDoctrine()->getRepository(Beer::class)->find($id);
			// dump($beer);exit;

			$data = [
				'status' => 'error',
				'code' => 400,
				'message' => 'could not find beer'
			];

			if ($beer && is_object($beer)) {
				$data = [
					'status' => 'success',
					'code' => 200,
					'beer' => $beer
				];
			}
		}

		return $this->jsonResp($data);
	}

	public function delete(Request $request, JwtAuth $jwtAuth, $id = null)
	{
		$data = [
			'status' => 'error',
			'code' => 400,
			'message' => 'something went wrong'
		];

		$token = $request->headers->get('Authorization', null);
		$auth_check = $jwtAuth->checkToken($token);

		if ($auth_check && !empty($id)) {
			$doctrine = $this->getDoctrine();
			$em = $doctrine->getManager();
			$beer = $doctrine->getRepository(Beer::class)->find($id);

			if ($beer && is_object($beer)) {
		        unlink(__DIR__.'/../../../front/src/assets/img/beers/'.$filename.'.png');
		        unlink(__DIR__.'/../../../front/src/assets/img/beers/'.$filename.'_small.png');

				$em->remove($beer);
				$em->flush();

				$data = [
					'status' => 'success',
					'code' => 200,
					'message' => 'beer deleted'
				];
			}
		}

		return $this->jsonResp($data);
	}
}
