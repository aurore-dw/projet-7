<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        //Tableau de noms de téléphone
        $phoneNames = [
            "iPhone 13 Pro",
            "Samsung Galaxy S21 Ultra",
            "Google Pixel 6 Pro",
            "OnePlus 9 Pro",
            "Xiaomi Mi 11",
            "Huawei P40 Pro",
            "Sony Xperia 5 III",
            "Oppo Find X3 Pro",
            "LG Velvet",
            "Motorola Edge+",
            "Nokia 8.3",
            "ASUS ROG Phone 5",
            "Vivo X60 Pro",
            "Realme GT",
            "Lenovo Legion Phone Duel 2",
            "BlackBerry KEY2",
            "HTC U12+",
            "ZTE Axon 30 Ultra",
            "OnePlus Nord 2",
            "Xiaomi Redmi Note 11 Pro",
        ];

        // Création d'une vingtaine de téléphones mobiles
        for ($i = 0; $i < 20; $i++) {
            $phone = new Phone;
            $randomNameIndex = array_rand($phoneNames);
            $phone->setName($phoneNames[$randomNameIndex]);
            $phone->setDescription('Description ' . $i);
            $randomPrice = rand(100, 999); 
            $phone->setPrice('Prix : ' . $randomPrice . ' €');
            $manager->persist($phone);

            // Retire le nom de téléphone utilisé du tableau
            unset($phoneNames[$randomNameIndex]);
            // Réindexe le tableau pour éviter les clés manquantes
            $phoneNames = array_values($phoneNames);
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
        // 10 utilisateurs Orange
        for($i=1; $i<=10; $i++)
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
        
        // 10 utilisateurs SFR
        for($i=1; $i<=10; $i++)
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

        // 10 utilisateurs Bouygues
        for($i=1; $i<=10; $i++)
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

         //Création d'utilisateur admin
        $admin = new User();
        $adminClient = $this->getReference('Orange');
        $admin->setUsername('admin')
            ->setEmail('admin@test.com')
            ->setPassword(password_hash("admin", PASSWORD_DEFAULT))
            ->setClient($adminClient)
            ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        //Création d'utilisateur standard
        $standardUser = new User();
        $standardClient = $this->getReference('Orange');
        $standardUser->setUsername('standard_user')
            ->setEmail('standard_user@test.com')
            ->setPassword(password_hash("user", PASSWORD_DEFAULT))
            ->setClient($standardClient)
            ->setRoles(["ROLE_USER"]);

        $manager->persist($standardUser);


        $manager->flush();
    }
}
