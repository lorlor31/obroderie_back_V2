<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->seed(1234);
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));

        $genreList = $faker->movieGenres(15);
        dump($genreList);
        // ['horreur', 'comédie', 'action', ]

        $insertedGenres = [];
        foreach ($genreList as $currentGenre)
        {
            if (in_array($currentGenre, $insertedGenres)) 
            {
                // si le genre a déjà été ajouté, on passe à la ligne suivante
                continue;
            }
            $genre = new Genre();
            $genre->setName($currentGenre);

            $manager->persist($genre);
            $insertedGenres[] = $currentGenre;
        }


        $manager->flush();
    }
}