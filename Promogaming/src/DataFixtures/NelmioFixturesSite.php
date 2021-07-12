<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Nelmio\Alice\Loader\NativeLoader;   
use Nelmio\Alice\Faker\Provider\AliceProvider;

class NelmioFixturesSite extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $faker = Factory::create();
        $faker->addProvider(new AliceProvider($faker));
        $loader = new NativeLoader($faker);
        $listUrls = [ 'url1','url2','url1','url1','url1'];
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__ . '/fixtures.yaml')->getObjects();

        //empile la liste d'objet à enregistrer en BDD
        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }

}
