<?php

namespace App\Controller\CheapShark;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\PlateformGame;
use App\Repository\GameRepository;
use App\Repository\PlateformGameRepository;
use App\Repository\PlateformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/cheapshark", name="cheapshark_")
 */
class CheapsharkController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/getAllGames", name="get_all_games")
     */
    public function getGamesByStore(PlateformRepository $plateformRepository, GameRepository $gameRepository, PlateformGameRepository $plateformGameRepository)
    {

        $plateforms = $plateformRepository->findAll();
        $var = 0;
        foreach ($plateforms as $plateform) {

            if ($plateform->getIsActive() == 1) {
                $var++;
                echo $var . '<br>';
                $storeID = $plateform->getApistoreId();
                $em = $this->getDoctrine()->getManager();
                $response = $this->client->request(
                    'GET',
                    'https://www.cheapshark.com/api/1.0/deals?storeID=' . $storeID . '&sortBy=Release&desc=0&metacritic=80&AAA=1&onSale=1'
                );
                $content = $response->toArray();

                foreach ($content as $data) {
                    if ($data['isOnSale'] !== 0) {
                        $gameExist = $gameRepository->findBy(['game_id_cheapshark' => $data['gameID']]);

                        //It's a new game, it does'nt exist in db
                        if ($gameExist == null) {
                            $game = new Game();
                            $game->setName($data['title']);
                            $game->setGameIdCheapshark($data['gameID']);
                            $plateformGame = new PlateformGame();
                            $plateformGame->setInitialPrice($data['normalPrice']);
                            $plateformGame->setActualPrice($data['salePrice']);
                            $plateformGame->setReduce(round($data['savings']));
                            $plateformGame->setDealID($data['dealID']);
                            $plateformGame->setIsActive(1);
                            $plateformGame->setPlateform($plateform);
                            $game->addPlateformGame($plateformGame);
                            $em->persist($game);
                        } else {
                            //Game exist in Game table : $gameExist !== null
                            //We wanna check if actual PlateformeGame exist in PlateformGame table with the plateform analyzed
                            //(1Game is unique and can exist in few Plateforms)
                            $plateformGameExist = $plateformGameRepository->findByGameAndPlateform($gameExist[0], $data['storeID']);
                            //S'il n'existe pas
                            if (empty($plateformGameExist)) {
                                $plateformGame = new PlateformGame();
                                $plateformGame->setInitialPrice($data['normalPrice']);
                                $plateformGame->setActualPrice($data['salePrice']);
                                $plateformGame->setReduce(round($data['savings']));
                                $plateformGame->setDealID($data['dealID']);
                                $plateformGame->setIsActive(1);
                                $plateformGame->setPlateform($plateform);
                                $plateformGame->setGame($gameRepository->find($gameExist[0]->getId()));
                                $em->persist($plateformGame);
                            // The plateformGame exist, maybe we need to update it
                            }else{
                                if ($data['isOnSale'] == 0 && $plateformGameExist[0]->getIsActive() == 1){
                                    $plateformGameExist[0]->setIsActive(0);
                                    $plateformGameExist[0]->setInitialPrice($data['normalPrice']);
                                    $plateformGameExist[0]->setActualPrice($data['salePrice']);
                                    $plateformGameExist[0]->setReduce(round($data['savings']));
                                    $plateformGameExist[0]->setDealID($data['dealID']);
                                    $em->persist($plateformGameExist[0]);
                                }elseif($data['isOnSale'] == 1 && $plateformGameExist[0]->getIsActive() == 0){
                                    $plateformGameExist[0]->setIsActive(0);
                                    $plateformGameExist[0]->setInitialPrice($data['normalPrice']);
                                    $plateformGameExist[0]->setActualPrice($data['salePrice']);
                                    $plateformGameExist[0]->setReduce(round($data['savings']));
                                    $plateformGameExist[0]->setDealID($data['dealID']);
                                    $em->persist($plateformGameExist[0]);
                                }
                            }
                        }
                    }
                }
                $em->flush();
            }
        }
        return $this->json('ok');
    }

    /**
     * @Route("/getAllStores", name="get_all_stores", methods={"GET"})
     * This method get all stores (plateforms) and insert into db if it doesn't exist
     */
    public function AllStores(PlateformRepository $repository)
    {

        $response = $this->client->request(
            'GET',
            'https://www.cheapshark.com/api/1.0/stores'
        );

        $content = $response->toArray();
        $em = $this->getDoctrine()->getManager();
        
        foreach ($content as $data) {
            
            $existingPlateform = $repository->findBy(["apistore_id" => $data["storeID"]]);
            if (empty($existingPlateform)) {
                $CSplateform = new Plateform();
                $CSplateform->setName($data['storeName']);
                $CSplateform->setIsActive($data['isActive']);
                $CSplateform->setApistoreId($data['storeID']);
                $em->persist($CSplateform);
            }
        }

        $em->flush();
        return $this->json('Insertion ok');
    }

    /**
     * @Route("/updateStores", name="update_stores")
     * Method update status (isActive (bool)) of stores (plateform)
     */
    public function updateStores(PlateformRepository $repository)
    {

        $response = $this->client->request(
            'GET',
            'https://www.cheapshark.com/api/1.0/stores'
        );

        $content = $response->toArray();
        $em = $this->getDoctrine()->getManager();
        $updatedContent = [];
        foreach ($content as $data) {

            $existingPlateform = $repository->findBy(["apistore_id" => $data["storeID"]]);
            // It's basic debug informations
            if ($existingPlateform[0]->getIsActive() === false) { $isActive = 0;} else { $isActive = 1;}
            echo $data['isActive'] . ' - ' . $isActive . ' /// ';

            //This part check status and change it if necessary
            if ($data['isActive'] === 1 && $existingPlateform[0]->getIsActive() === false) {
                $toPersist = $existingPlateform[0]->setIsActive(true);
                $em->persist($toPersist);
                array_push($updatedContent, ['New status ' . $toPersist->getName() => $toPersist->getIsActive()]);
            } elseif ($data['isActive'] === 0 && $existingPlateform[0]->getIsActive() === true) {
                $toPersist = $existingPlateform[0]->setIsActive(false);
                $em->persist($toPersist);
                array_push($updatedContent, ['New status ' . $toPersist->getName() => $toPersist->getIsActive()]);
            }
        }

        $em->flush();
        return $this->json($updatedContent);
    }
}
