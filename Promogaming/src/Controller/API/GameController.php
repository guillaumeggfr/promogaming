<?php

namespace App\Controller\API;

use App\Entity\Tag;
use App\Repository\PlateformGameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GameRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/api/games/", name="api_games_")
 * 
 */
class GameController extends AbstractController
{
    private $paginator;
    private $serializer;
    private $cache;
    private $gameRepository;
    private $plateformRepository;
    private $tag;
    private $page;
    private $plateformId;

    public function __construct(
        PaginatorInterface $paginator,
        SerializerInterface $serializer,
        CacheInterface $cache,
        GameRepository $gameRepository,
        PlateformGameRepository $plateformRepository
    ) {
        $this->paginator = $paginator;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->gameRepository = $gameRepository;
        $this->plateformRepository = $plateformRepository;
    }

    /**
     * @Route("all", name="all", methods={"GET"})
     * Road for all games
     */
    public function getAllGames(): Response
    {
        $games = $this->cache->get(
            'AllGames',
            function (ItemInterface $item) {
                //Cache doesn't exist, i got to make request to my db to get content and insert into cache,
                //then i'll be able to send the cache to the front website
                $item->expiresAfter(43200);
                return $this->serializer->serialize(
                    ($this->plateformRepository->findBy(['isActive' => 1])),
                    'json',
                    ['groups' => ['api_games_all']]
                );
            }
        );

        //I return all games according to their plateform
        return new Response($games, Response::HTTP_OK, []);
    }



    /**
     * @Route("{id}", name="gamesByIdGame", methods={"GET"}, requirements={"id"="\d+"})
     * Road for the game with id = $id
     */
    public function getGamesbyId($id): Response
    {
        //I return plateformgames by id
        return $this->json($this->plateformRepository->getGamesByIdGame($id), Response::HTTP_OK, [], [
            'groups' => ['api_games_id'],
        ]);
    }

    /**
     * @Route("GameBytag/{id}", name="id", methods={"GET"})
     * Road for games by tag
     */
    public function findbyTag(Tag $tag, Request $request): Response
    {
        $this->page = $request->query->get('page', 1);
        $this->tag = $tag;

        $games = $this->cache->get(
            'gamesByTag-' . $tag->getId() . '-' . $this->page,
            function (ItemInterface $item) {
                //Cache doesn't exist, i got to make request to my db to get content and insert into cache,
                //then i'll be able to send the cache to the front website
                $item->expiresAfter(43200);
                return $this->serializer->serialize(
                    $this->paginator->paginate($this->gameRepository->findGamesByTag($this->tag), $this->page, 20),
                    'json',
                    ['groups' => ['api_games_bytag']]
                );
            }
        );

        //I return all games according to their tag
        return new Response($games, Response::HTTP_OK, []);
    }

    /**
     * @Route("plateforms/{id}", name="gamesByPlateform", methods={"GET"}, requirements={"id"="\d+"})
     * Road for games by plateform
     */
    public function getGamesbyPlateform( Request $request, $id): Response
    {
        $this->page = $request->query->get((int)'page', 1);
        $this->plateformId = $id;
        //First i try to get the page from cache
        $games = $this->cache->get(
            'gamesByPlateform-' . $this->plateformId . '-' . $this->page,
            //Cache doesn't exist, i got to make request to my db to get content and insert into cache,
            //then i'll be able to send the datapage to the front website
            function (ItemInterface $item) {
                $item->expiresAfter(43200);
                return $this->serializer->serialize(
                    $this->paginator->paginate($this->plateformRepository->getGamesByPlateform($this->plateformId), $this->page, 20),
                    'json',
                    ['groups' => ['api_games_id']]
                );
            }
        );
        
        //I return all games according to their plateform
        return new Response($games, Response::HTTP_OK, []);
    }

    /**
     * @Route("reduce/{value}", name="reduce", methods={"GET"}, requirements={"value"="\d+"})
     * Road for games by reduce
     */
    public function getGamesbyReduce($value): Response
    {

        //I return all games according to reduce where value is better than $value
        return $this->json($this->plateformRepository->getGamesByReduce($value), Response::HTTP_OK, [], ['groups' => ['api_games_id']]);
    }
}
