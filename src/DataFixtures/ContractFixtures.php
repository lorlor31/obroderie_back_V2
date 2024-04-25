<?php

namespace App\DataFixtures;

use Faker\Factory;
use DateTimeImmutable;
use App\Entity\Contract;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class ContractFixtures extends Fixture implements DependentFixtureInterface

{
    public function __construct(private UserRepository $userRepos, 
    private CustomerRepository $customerRepos,)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->seed(1234);
        $faker->addProvider(new AppProvider());

//Contract fixtures
    for ($currentContract = 0; $currentContract < 50; $currentContract++)
    {
        //creating  a fictitious contract
        $contract = new Contract();
        //get all the customers list
        $customerList=$this->customerRepos->findAll() ;
         //get all the users list
        $userList=$this->userRepos->findAll() ;
        // setting fields with data
        $contract->setorderedAt($faker->dateTimeInInterval('- 4 weeks' , '+ 2 weeks' ));
        $contract->setinvoicedAt($faker->dateTimeInInterval('- 2 weeks ' , '+ 2 weeks'));
        $contract->setType($faker->randomElement($faker->getContractType()));
        $contract->setDeliveryAddress($faker->randomElement($faker->getContractDeliveryAddress()));
        $contract->setStatus($faker->randomElement($faker->getContractStatus()));
        $contract->setComment($faker->randomElement($faker->getContractComment()));
        $contract->setCreatedAt((DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days"))));
        $contract->setUser($faker->randomElement($userList));
        $contract->setCustomer($faker->randomElement($customerList));
        // persist in database
        $manager->persist($contract);
    }

     // Applying changes into the DB
     $manager->flush();
    }
    
    //launch the FourSimpleEntitiesFixtures before
    public function getDependencies()
    {
        return [
            FourSimpleEntitiesFixtures::class,
        ];
    }
}