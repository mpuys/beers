<?php
namespace App\Services;

use App\Entity\Beer;
use App\Repository\BeerRepository;
use App\Repository\BreweryRepository;
use App\Repository\TypeRepository;
use Knp\Component\Pager\PaginatorInterface;

class BeerService
{
	public $manager;
	public $beerRepo;
	public $typeRepo;

	public function __construct($manager, BeerRepository $beerRepo, BreweryRepository $brewRepo, TypeRepository $typeRepo, PaginatorInterface $paginator)
	{
		$this->manager = $manager;
		$this->beerRepo = $beerRepo;
        $this->brewRepo = $brewRepo;
		$this->typeRepo = $typeRepo;
        $this->paginator = $paginator;
	}

	public function newBeer($params)
	{
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'something went wrong in service, try again later'
        ];

		$name = !empty($params->name) ? $params->name : null;
        $description = !empty($params->description) ? $params->description : '';
        $abv = !empty($params->abv) || $params->abv == '0' ? $params->abv : null;
        $volume = !empty($params->volume) ? $params->volume : null;
        $misc = !empty($params->misc) ? $params->misc : '';
        $type_id = !empty($params->type->id) ? $params->type->id : null;
        $breweries = $params->brewery;

        if (!empty($name) && (!empty($abv) || $abv == '0') && !empty($volume) && !empty($breweries) && !empty($type_id)) {
            $type = $this->typeRepo->find($type_id);
            $breweries = $this->brewRepo->findBy(['id' => [$breweries->brewery1->id, $breweries->brewery2->id]]);
            $imgName = str_replace(' ', '_', strtolower($name)).'_@_';
            for($i=0; $i<sizeof($breweries); $i++) {
                $imgName .= $breweries[$i]->getName() ? str_replace(' ', '_', strtolower($breweries[$i]->getName())) : '';
                if ($i < sizeof($breweries)-1) {
                    $imgName .= '_+_';
                }
            }

            if ($params->id == 0) {
                $beer = new Beer();
                $beer->setName($name);
                $beer->setDescription($description);
                $beer->setAbv($abv);
                $beer->setVolume($volume);
                $beer->setMisc($misc);
                $beer->setImg($imgName);
                $beer->setType($type);

                foreach ($breweries as $brewery) {
                    $beer->addBrewery($brewery);
                }

                $this->manager->persist($beer);
                $this->manager->flush();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'beer saved successfully',
                    'beer' => $beer
                ];
            } else {
                $beer = $this->beerRepo->find($params->id);

                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'could not find specified beer'
                ];

                if ($beer && is_object($beer)) {
                    $beer->setName($name);
                    $beer->setDescription($description);
                    $beer->setAbv($abv);
                    $beer->setVolume($volume);
                    $beer->setMisc($misc);
                    $beer->setType($type);

                    $oldImgName = $beer->getImg();
                    if ($oldImgName != $imgName) {
                        $beer->setImg($imgName);
                        $imgDirectory = __DIR__.'/../../../front/src/assets/img/beers/';
                        unlink($imgDirectory.$oldImgName.'.png');
                        unlink($imgDirectory.$oldImgName.'_small.png');
                    }

                    $oldBrews = $beer->getBreweries();
                    foreach ($oldBrews as $brew) {
                        $beer->removeBrewery($brew);
                    }

                    foreach ($breweries as $brewery) {
                        $beer->addBrewery($brewery);
                    }

                    $this->manager->persist($beer);
                    $this->manager->flush();

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'beer saved successfully',
                        'beer' => $beer
                    ];
                }
            }
        }

        return $data;
	}

    public function saveImgs($img, $beer) {
        $brew1_id = $beer->brewery->brewery1->id;
        $brew2_id = $beer->brewery->brewery2 ? $beer->brewery->brewery2->id : '';
        $breweries = $this->brewRepo->findBy(['id' => [$brew1_id, $brew2_id]]);

        $filename = str_replace(' ', '_', strtolower($beer->name)).'_@_';
        for($i=0; $i<sizeof($breweries); $i++) {
            $filename .= $breweries[$i]->getName() != '' ? str_replace(' ', '_', strtolower($breweries[$i]->getName())) : '';
            if ($i < sizeof($breweries)-1) {
                $filename .= '_+_';
            }
        }

        move_uploaded_file($img, __DIR__.'/../../../front/src/assets/img/beers/'.$filename.'.png');

        $source_image = imagecreatefrompng( __DIR__.'/../../../front/src/assets/img/beers/'.$filename.'.png');
        $source_imagex = imagesx($source_image);
        $source_imagey = imagesy($source_image);

        $dest_imagex = 200;
        $dest_imagey = 415;
        $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);
        imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex, $dest_imagey, $source_imagex, $source_imagey);
        
        imagepng($dest_image,  __DIR__.'/../../../front/src/assets/img/beers/'.$filename.'_small.png', 9);
    }
}