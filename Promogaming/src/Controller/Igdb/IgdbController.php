<?php

namespace App\Controller\Igdb;

use App\Service\igdbAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/igdb", name="igdb_")
 */
class IgdbController extends AbstractController
{
    private $client;
    private $igdb;

    public function __construct(HttpClientInterface $client, igdbAPI $igdb)
    {
        $this->client = $client;
        $this->igdb = $igdb;
    }

    
    /**
     * @Route("/getGameDataMissing", name="get_game_data_missing")
     * It add description and game_igdb_id to our db
     * 
     */
    public function getGameDataMissing(): Response
    {
        $gamesList = $this->igdb->getDescriptionAndIgdbID();

        return $this->json($gamesList);
    }

    /**
     * @Route("/search", name="search")
     * Search method to customize, for tests 
     */
    public function search(): Response
    {
        $token = $this->igdb->getTokenAccess();
        $response = $this->client->request(
            'POST',
            'https://api.igdb.com/v4/covers',
            [
                'headers' => [
                    'Client-ID: ' . $_ENV['TWITCH_CLIENTID'] . '',
                    'Authorization: Bearer ' . $token . '',
                ],
                'body' => 'fields *;
                where game = 231;'
            ]
        );
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];

        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        return $this->json($content);
    }

    /**
     * @Route("/getGamesTags", name="get_games_tags")
     * Get tags of games and insert with relations in db
     */
    public function getGamesTags(): Response
    {
        $gamesTags = $this->igdb->getGamesTags();

        return $this->json($gamesTags);
    }

    /**
     * @Route("/remove", name="remove_null")
     * Not working, actually we get GameList by CheapShark API (deals)
     * Titles in Gamelist could not match with IGDB API title games, so we can't retrieve missing datas as description and images
     * This was to delete games not matching, but we update them manually
     */
    public function removeNull(): Response
    {
        $nullGames = $this->igdb->removeNullGames();

        return $this->json($nullGames);
    }

    /**
     * @Route("/getGamesImages", name="get_games_images")
     */
    public function getGamesImages(): Response
    {
        $gamesScreenshots = $this->igdb->getGamesImages();

        return $this->json($gamesScreenshots);
    }
    /**
     * @Route("/getAllTags", name="get_all_genres")
     */
    public function getAllTags(): Response
    {
        $tags = $this->igdb->getAllTags();
        return $this->json($tags);
    }
}
