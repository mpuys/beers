<?php
namespace App\Services;

use App\Entity\Brewery;
use App\Repository\BreweryRepository;
use App\Repository\CountryRepository;
use App\Services\CountryService;

class BreweryService
{
	public $manager;
	public $brewRepo;
	public $countryRepo;

	public function __construct($manager, BreweryRepository $brewRepo, CountryRepository $countryRepo, CountryService $countryService)
	{
		$this->manager = $manager;
		$this->brewRepo = $brewRepo;
        $this->countryRepo = $countryRepo;
		$this->countryService = $countryService;
	}

	public function new($params)
	{
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong, try again later'
        ];

		$name = !empty($params->name) ? $params->name : null;
        $url = !empty($params->url) ? $params->url : null;
        $logo = !empty($params->logo) ? $params->logo : null;
        $address = !empty($params->address) ? $params->address : null;

        if ($params->country->id == 0 || $params->country->id == null) {
            $country = $this->countryService->new($params->country);
        } else if ($params->country->id != 0 && $params->country->id != null) {
            $country = $this->countryRepo->find($params->country->id);
        }

        if (!empty($name) && !empty($url) && !empty($address) && !empty($country)) {
        	$brewery = $this->brewRepo->findOneBy(['name' => $params->name]);

        	if (is_object($brewery)) {
        		return [
		            'status' => 'error',
		            'code' => 400,
		            'message' => 'brewery already exists'
        		];
        	}

            $brewery = new Brewery();
            $brewery->setName($name);
            $brewery->setUrl($url);
            $brewery->setLogo($logo);
            $brewery->setAddress($address);
            $brewery->setCountry($country);

            $this->manager->persist($brewery);
            $this->manager->flush();

            $id = $brewery->getId();
            $brewery = $this->brewRepo->find($id);

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'brewery saved successfully',
                'brewery' => $brewery
            ];
        }

        return $data;
	}

	public function edit($params, $id)
	{
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong, try again later'
        ];

        if (!empty($params->name)) {
        	$brewery = $this->brewRepo->find($id);

        	if (!is_object($brewery)) {
        		return [
		            'status' => 'error',
		            'code' => 400,
		            'message' => 'could not find brewery'
		        ];
        	}
            
            $brewery->setName($params->name);
            $brewery->setUrl($params->url);
            $brewery->setLogo($params->logo);
            $brewery->setAddress($params->address);

        	$country = $this->countryRepo->find($params->country_id);
            $brewery->setCountry($country);

            // $this->manager->persist($brewery);
            // $this->manager->flush();

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'brewery edited successfully',
                'brewery' => $brewery
            ];
        }

        return $data;
	}
}