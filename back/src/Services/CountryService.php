<?php
namespace App\Services;

use App\Entity\Country;
use App\Repository\CountryRepository;

class CountryService
{
	public $manager;
	public $repo;

	public function __construct($manager, CountryRepository $repo)
	{
		$this->manager = $manager;
		$this->repo = $repo;
	}

	public function new($params)
	{
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong, try again later'
        ];

        if (!empty($params->name)) {
        	$country = $this->repo->findOneBy(['name' => $params->name]);

        	if (is_object($country)) {
        		return [
		            'status' => 'error',
		            'code' => 400,
		            'message' => 'country already exists'
        		];
        	}

            $country = new Country();
            $country->setName($params->name);

            $this->manager->persist($country);
            $this->manager->flush();

            $id = $country->getId();
            $country = $this->repo->find($id);

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'country saved successfully',
                'country' => $country
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
        	$country = $this->repo->find($id);

        	if (!is_object($country)) {
        		return [
		            'status' => 'error',
		            'code' => 400,
		            'message' => 'could not find country'
		        ];
        	}

            $country->setName($params->name);

            $this->manager->persist($country);
            $this->manager->flush();

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'country edited successfully',
                'country' => $country
            ];
        }

        return $data;
	}
}