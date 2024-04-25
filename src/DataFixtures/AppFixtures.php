<?php

//this file must create the product fixture and will have the ultime
namespace App\DataFixtures;

use Faker\Factory;
use DateTimeImmutable;
use App\Entity\Product;
use App\Repository\TextileRepository;
use App\Repository\ContractRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\EmbroideryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private ContractRepository $contractRepos, private TextileRepository $textileRepos, private EmbroideryRepository $embroideryRepos)
    {
    }
  
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->seed(1234);
        $faker->addProvider(new AppProvider());

        //get all the customers list
        $contractList=$this->contractRepos->findAll() ;
        $textileList=$this->textileRepos->findAll() ;
        $embroideryList=$this->embroideryRepos->findAll() ;

        //creating some products
        for ($i = 0; $i < 60; $i++)
        {
            //creating  a fictitious product
            $product = new Product();
            // setting fields with data
            $product->setdeliveryAt($faker->dateTimeInInterval('now' , '+ 2 weeks' ));
            $product->setName($faker->randomElement($faker->getProductName()));
            $product->setQuantity($faker->numberBetween(1, 100));
            $product->setPrice($faker->randomFloat(2,0,100));
            $product->setManufacturingDelay($faker->numberBetween(1, 10));
            $product->setProductOrder($faker->numberBetween(1, 10));
            $product->setComment($faker->randomElement($faker->getProductComment()));
            $product->setCreatedAt((DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days"))));
            $product->setContract($faker->randomElement($contractList));
            $product->setTextile($faker->randomElement($textileList));
            $product->setEmbroidery($faker->randomElement($embroideryList));
            $manager->persist($product);
        }
            // record in database
            $manager->flush();
        }
        //launch the ContractFixtures before
        public function getDependencies()
        {
            return [
                ContractFixtures::class,
            ];
        }

}