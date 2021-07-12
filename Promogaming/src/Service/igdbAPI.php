<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Tag;
use App\Repository\GameRepository;
use App\Repository\PlateformGameRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class igdbAPI
{

    private $client;
    private $em;
    private $tagRepository;
    private $gameRepository;
    private $pgRepository;

    public function __construct(
        HttpClientInterface $client,
        EntityManagerInterface $em,
        TagRepository $tagRepository,
        GameRepository $gameRepository,
        PlateformGameRepository $pgRepository
    ) {
        $this->client = $client;
        $this->em = $em;
        $this->tagRepository = $tagRepository;
        $this->gameRepository = $gameRepository;
        $this->pgRepository = $pgRepository;
    }

    //Method to get Token Access, Oauth2.0
    public function getTokenAccess(): string
    {
        $response = $this->client->request(
            'POST',
            'https://id.twitch.tv/oauth2/token?client_id=' . $_ENV['TWITCH_CLIENTID'] . '&client_secret=' . $_ENV['TWITCH_SECRET'] . '&grant_type=client_credentials',
            []
        );
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        return json_decode($response->getContent());
    }
    /**
     * 
     * NOT WORKING, BASE MODEL FOR NEXT VERSION, USING COMMANDS 
     */
    public function getAllGames(): string
    {
        $games = [];
        $token = $this->getTokenAccess();
        $request = 50;
        $offset = 0;
        $i = 0;
        for ($i = 0; $offset < $request; $i++) {
            $limit = 50;
            $offset = $limit * $i;
            $response = $this->client->request(
                'POST',
                'https://api.igdb.com/v4/games',
                [
                    'headers' => [
                        'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                        'Authorization: Bearer ' . $token . '',
                    ],
                    'body' => 'fields name, genres.name;sort id asc;where category = 0;limit ' . $limit . '; offset ' . $offset . ';'
                ]
            );
            $statusCode = $response->getStatusCode();
            // $statusCode = 200
            $contentType = $response->getHeaders()['content-type'][0];
            // $contentType = 'application/json'
            $content = $response->getContent();
            array_push($games, $content);
        }
        dd($games);
    }


    public function getDescriptionAndIgdbID()
    {
        $token = $this->getTokenAccess();

        //First i need to get my games List
        $gamesList = $this->gameRepository->findAll();
        foreach ($gamesList as $game) {
            $response = $this->client->request(
                'POST',
                'https://api.igdb.com/v4/games',
                [
                    'headers' => [
                        'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                        'Authorization: Bearer ' . $token . '',
                    ],
                    'body' => 'fields name, summary;
                    where name = "' . $game->getName() . '";'
                ]
            );
            $statusCode = $response->getStatusCode();
            // $statusCode = 200
            $contentType = $response->getHeaders()['content-type'][0];

            // $contentType = 'application/json'
            $content = json_decode($response->getContent());
            if (isset($content[0]->summary) && isset($content[0]->id)) {
                $game->setDescription($content[0]->summary);

                $game->setGameIdIgdb($content[0]->id);

                $this->em->persist($game);
            }
        }
        $this->em->flush();
        return 'Succes, summary and IGDB id addes to Games';
    }

    public function getGamesTags()
    {
        $token = $this->getTokenAccess();
        //First i need to get my games List
        $gamesList = $this->gameRepository->findAll();
        foreach ($gamesList as $game) {
            if ($game->getTags() !== null) {
                $response = $this->client->request(
                    'POST',
                    'https://api.igdb.com/v4/games',
                    [
                        'headers' => [
                            'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                            'Authorization: Bearer ' . $token . '',
                        ],
                        'body' => 'fields genres;
                    where name = "' . $game->getName() . '";'
                    ]
                );
                $statusCode = $response->getStatusCode();
                // $statusCode = 200
                $contentType = $response->getHeaders()['content-type'][0];

                // $contentType = 'application/json'
                //$content = $response->getContent();
                $content = $response->toArray();
                if (isset($content[0]['genres'])) {
                    foreach ($content[0]['genres'] as $genre) {
                        $tag = $this->tagRepository->findOneBy(['igdb_id' => $genre]);
                        $game->addTag($tag);
                        $this->em->persist($game);
                    }
                }
            }
        }
        $this->em->flush();
        return 'Succes, tags added to games';
    }

    public function removeNullGames()
    {
        $gamesList = $this->gameRepository->findBy(['description' => null]);
        foreach ($gamesList as $game) {
            $pgs = $this->pgRepository->findBy(['game' => $game->getId()]);
            foreach ($pgs as $pg) {
                $game->removePlateformGame($pg);
            }
            dd($game);
            $tags = $game->getTags();
            dd($tags);
            foreach ($tags as $tag) {
                $game->removeTag($tag);
            }
            $this->em->remove($game);
        }
        $this->em->flush();
        return $gamesList;
    }

    public function getGamesImages()
    {
        $token = $this->getTokenAccess();
        //First i need to get my games List
        $gamesList = $this->gameRepository->findAll();
        foreach ($gamesList as $game) {
            //1st image is cover
            if ($game->getImages()->count() > 1) {
                $id = $game->getGameIdIgdb();
                if ($id !== null) {
                    $response = $this->client->request(
                        'POST',
                        'https://api.igdb.com/v4/covers',
                        [
                            'headers' => [
                                'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                                'Authorization: Bearer ' . $token . '',
                            ],
                            'body' => 'fields image_id;
                        where game = ' . $id . ';'
                        ]
                    );
                    $statusCode = $response->getStatusCode();
                    // $statusCode = 200
                    $contentType = $response->getHeaders()['content-type'][0];

                    // $contentType = 'application/json'
                    //$content = $response->getContent();
                    $content = $response->toArray();
                    // if there is covers, we add them
                    if (isset($content) && !empty($content)) {
                        foreach ($content as $image) {
                            $extension = '.jpg';
                            $baseUrl = 'https://images.igdb.com/igdb/image/upload/t_720p/';
                            $url = $baseUrl . $image['image_id'] . $extension;
                            $imageInsert = new Image();
                            $imageInsert->setIdIgdb($image['id']);
                            $imageInsert->setUrl($url);
                            $game->addImage($imageInsert);
                            $this->em->persist($game);
                        }
                    }
                }
                // if there is already 1 image (cover), then we search for screenshots
            } elseif ($game->getImages()->count() == 1) {
                $id = $game->getGameIdIgdb();
                if ($id !== null) {
                    $response = $this->client->request(
                        'POST',
                        'https://api.igdb.com/v4/games',
                        [
                            'headers' => [
                                'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                                'Authorization: Bearer ' . $token . '',
                            ],
                            'body' => 'fields screenshots.image_id;
                        where id = ' . $id . ';'
                        ]
                    );
                    $statusCode = $response->getStatusCode();
                    // $statusCode = 200
                    $contentType = $response->getHeaders()['content-type'][0];

                    $content = $response->toArray();
                    //if there is screenshots, we add them
                    if (isset($content[0]['screenshots']) && !empty($content[0]['screenshots'])) {
                        foreach ($content[0]['screenshots'] as $images) {
                            $extension = '.jpg';
                            $baseUrl = 'https://images.igdb.com/igdb/image/upload/t_720p/';
                            $url = $baseUrl . $images['image_id'] . $extension;
                            $imageInsert = new Image();
                            $imageInsert->setIdIgdb($images['id']);
                            $imageInsert->setUrl($url);
                            $game->addImage($imageInsert);
                            $this->em->persist($game);
                        }
                        $this->em->flush();
                    }
                }
            }
        }
        $this->em->flush();
        return 'Succes, Screens added to games';
    }

    public function getAllTags(): string
    {
        $token = $this->getTokenAccess();
        $response = $this->client->request(
            'POST',
            'https://api.igdb.com/v4/genres',
            [
                'headers' => [
                    'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                    'Authorization: Bearer ' . $token . '',
                ],
                'body' => 'fields name;sort id asc;limit 350;'
            ]
        );
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = json_decode($response->getContent());
        $existingTags = $this->tagRepository->findAll();
        foreach ($content as $data) {
            if (empty($existingTags)) {
                $tag = new Tag();
                $tag->setName($data->name);
                $tag->setIgdbId($data->id);
                $this->em->persist($tag);
            } else {
                $existingTag = $this->tagRepository->findBy(['igdb_id' => $data->id]);
                if (!$existingTag) {
                    $tag = new Tag();
                    $tag->setName($data->name);
                    $tag->setIgdbId($data->id);
                    $this->em->persist($tag);
                }
            }
        }
        $this->em->flush();
        return 'Succes, tags added';
    }
}
