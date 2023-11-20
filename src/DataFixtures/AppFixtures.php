<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'une vingtaine de téléphones mobiles
        for ($i = 0; $i < 20; $i++) {
            $phone = new Phone;
            $phone->setName('Téléphone mobile ' . $i);
            $phone->setDescription('Description ' . $i);
            $randomPrice = rand(100, 999); 
            $phone->setPrice('Prix : ' . $randomPrice);
            $manager->persist($phone);
        }

        //Création de client
        $client = new Client();
        $client->setName('Orange');
        $this->addReference('Orange', $client);
        $manager->persist($client);

        $client = new Client();
        $client->setName('SFR');
        $this->addReference('SFR', $client);
        $manager->persist($client);

        $client = new Client();
        $client->setName('Bouygues');
        $this->addReference('Bouygues', $client);
        $manager->persist($client);

        //Création d'utilisateur
        // 5 users from Orange
        for($i=1; $i<=5; $i++)
        {
            $user = new User();
            $client = $this->getReference('Orange');
            $user->setUsername('user'.$i.$client->getName())
                ->setEmail('user'.$i.$client->getName().'@test.com')
                ->setPassword(password_hash("1234", PASSWORD_DEFAULT))
                ->setClient($client)
                ->setRoles(["ROLE_USER"])
            ;
            $manager->persist($user);
        }
        
        // 5 users from SFR
        for($i=1; $i<=5; $i++)
        {
            $user = new User();
            $client = $this->getReference('SFR');
            $user->setUsername('user'.$i.$client->getName())
                ->setEmail('user'.$i.$client->getName().'@test.com')
                ->setPassword(password_hash("1234", PASSWORD_DEFAULT))
                ->setClient($client)
                ->setRoles(["ROLE_USER"])
            ;
            $manager->persist($user);
        }

        // 5 users from Bouygues
        for($i=1; $i<=5; $i++)
        {
            $user = new User();
            $client = $this->getReference('Bouygues');
            $user->setUsername('user'.$i.$client->getName())
                ->setEmail('user'.$i.$client->getName().'@test.com')
                ->setPassword(password_hash("1234", PASSWORD_DEFAULT))
                ->setClient($client)
                ->setRoles(["ROLE_USER"])
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}
