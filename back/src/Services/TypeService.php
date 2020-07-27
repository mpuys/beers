<?php
namespace App\Services;

use App\Entity\Type;
use App\Repository\TypeRepository;

class TypeService
{
	public $manager;
	public $repo;

	public function __construct($manager, TypeRepository $repo)
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
        	$type = $this->repo->findOneBy(['name' => $params->name]);

        	if (is_object($type)) {
        		return [
		            'status' => 'error',
		            'code' => 400,
		            'message' => 'type already exists'
        		];
        	}

            $type = new Type();
            $type->setName($params->name);

            $this->manager->persist($type);
            $this->manager->flush();

            $id = $type->getId();
            $type = $this->repo->find($id);

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'type saved successfully',
                'type' => $type
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
        	$type = $this->repo->find($id);

        	if (!is_object($type)) {
        		return [
		            'status' => 'error',
		            'code' => 400,
		            'message' => 'could not find type'
		        ];
        	}

            $type->setName($params->name);

            $this->manager->persist($type);
            $this->manager->flush();

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'type edited successfully',
                'type' => $type
            ];
        }

        return $data;
	}
}