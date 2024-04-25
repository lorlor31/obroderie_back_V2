<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Textile;
use App\Entity\Contract;
use App\Entity\Customer;
use App\Entity\Embroidery;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Generator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FourSimpleEntitiesFixtures extends Fixture

{
    public function __construct( private UserPasswordHasherInterface $passwordHasher ) {}
    
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        $faker->seed(1234);
        $faker->addProvider(new AppProvider());
        

    //Embroidery fixtures

        for ($currentEmbroidery = 0; $currentEmbroidery < 50; $currentEmbroidery++)
        {
            //creating  a fictitious embroidery
            $embroidery = new Embroidery();
            // setting fields with data
            $embroidery->setName($faker->randomElement($faker->getEmbroideryName()));
            $embroidery->setDesign($faker->randomElement($faker->getEmbroideryDesign()));
            $embroidery->setText($faker->randomElement($faker->getEmbroideryText()));
            $embroidery->setDetail($faker->randomElement($faker->getEmbroideryDetail()));
            $embroidery->setCreatedAt((DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days"))));
            // persist in databse
            $manager->persist($embroidery);
        }  

    //Textile fixtures
        //creating  a fictitious embroidery
        for ($i=0; $i<50 ; $i++) {
            $textile = new Textile();
         // setting fields with data
            $textile->setName($faker->randomElement($faker->getTextileName()));
            $textile->setType($faker->randomElement($faker->getTextileType()));
            $textile->setSize($faker->randomElement($faker->getTextileSize()));
            $textile->setColor($faker->randomElement($faker->getTextileColor()));
            $textile->setBrand($faker->randomElement($faker->getTextileBrand()));
            $textile->setComment($faker->randomElement($faker->getTextileComment()));
            $textile->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days")));
            $manager->persist($textile);
        }

    //Customer fixtures
        //creating  a fictitious customer
        for ($i=0; $i<50 ; $i++) {
            $customer = new Customer();
         // setting fields with data
            $customer->setName($faker->randomElement($faker->getCustomerName()));
            $customer->setAddress($faker->randomElement($faker->getCustomerAddress()));
            $customer->setEmail($faker->randomElement($faker->getCustomerEmail()));
            $customer->setContact($faker->randomElement($faker->getCustomerContact()));
            $customer->setPhoneNumber($faker->randomElement($faker->getCustomerPhoneNumber()));
            $customer->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days")));
            $manager->persist($customer);
        }
    //User fixtures

            //Creating a user with user role
            $user = new User();
            $user->setPseudo('user') ;
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user')) ;
            $user->setRoles(['ROLE_USER']) ;
            $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days")));
            $manager->persist($user);

            $admin = new User();
            $admin->setPseudo('admin') ;
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin')) ;
            $admin->setRoles(['ROLE_ADMIN']) ;
            $admin->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days")));
            $manager->persist($admin);
        // //creating  a fictitious user
        // for ($i=0; $i<2 ; $i++) {
        //     $user = new User();
        //  // setting fields with data
        //     $user->setPseudo($faker->unique->randomElement($faker->getUserPseudo()));
        //     $user->setPassword($faker->randomElement($faker->getUserPassword()));
        //     $user->setRoles($faker->randomElements(['ADMIN', 'USER'], 1));
        //     $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-30 days")));
        //     $manager->persist($user);
        // }
        // Applying changes into the DB
        $manager->flush();
    }
}
