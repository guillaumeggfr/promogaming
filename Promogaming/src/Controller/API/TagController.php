<?php

namespace App\Controller\API;

use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/tags", name="api_tags_")
 * 
 */
class TagController extends AbstractController
{
    private $repository;

    public function __construct(TagRepository $tagRepository )
    {
        $this->repository = $tagRepository;
    }
    /**
     * @Route("/all", name="all", methods={"GET"})
     */
    public function getAllTags(): Response
    {

        //I return Tags and status code
        return $this->json($this->repository->findAll(), Response::HTTP_OK, [], [
            'groups' => ['api_tags_all']
        ]);
    }
}