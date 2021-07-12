<?php

namespace App\Controller\API;

use App\Repository\PlateformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/plateforms", name="api_plateforms_")
 * 
 */
class PlateformController extends AbstractController
{
    /**
     * @Route("/all", name="allPlateform", methods={"GET"})
     */
    public function getAllPlateform(PlateformRepository $plateform): Response
    {
        //I return PlateformGames and status code
        return $this->json($plateform->findAll(), Response::HTTP_OK, [], [
            'groups' => ['plateforms'],
        ]);
    }
    /**
     * @Route("/{id}", name="PlateformId", methods={"GET"})
     */
    public function getPlateformId(PlateformRepository $plateform, $id): Response
    {
        //I return PlateformGames and status code
        return $this->json($plateform->find($id), Response::HTTP_OK, [], [
            'groups' => ['plateforms'],
        ]);
    }

    
}
