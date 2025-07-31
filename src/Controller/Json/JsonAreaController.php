<?php

namespace App\Controller\Json;
use App\Domain\Area\Entity\{Area};
use App\Domain\Area\Manager\{AreaManager};
use App\Domain\Geonames\Entity\{Country};
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\JsonApi\Serializers\{AreaSerializer};
use App\Shared\JsonApi\ErrorSerializer\JsonErrorBag;
use App\Shared\tobscure\jsonapi\{Collection, Document};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/json/area')]
class JsonAreaController extends AbstractController
{

    /**
     * @throws ORMException
     */
    #[Route(path: '/areaSubsetByCodeOrString', name: 'json_subset_area', methods: ["GET","POST"])]
    public function jsonAreaAction(Request $request, AreaManager $manager, EntityManagerInterface $em): JsonResponse
    {

        $area = new Area();

        if($code= $request->get('code'))
        {
            if (!strpos($code, "."))//country = ID
            {
                $area->setCountry($em->getReference(Country::class, $code));
            }else{
                $area->setAdmin1($em->getReference(Admin1::class, $code));
            }
        }else if($string= $request->get('string')){
            $area->setName($string);
        }else{
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "code" or "string" parameter'))->getJsonResponse();
        }

        $data= $manager->findByArea($area);
        //JSON-API
        $collection = (new Collection($data, new AreaSerializer()))
//            ->with(['country', 'area'])->fields(['country'=>['name'], 'area'=>['country', 'name', 'nameascii']]);
            ->with(['area'])->fields(['area'=>['name', 'comment']]);
        $document = new Document($collection);

        return new JsonResponse($document , 200);
    }
}