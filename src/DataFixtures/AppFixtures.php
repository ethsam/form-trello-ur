<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Format;
use App\Repository\UserRepository;
use App\Repository\FormatRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $manager;
    private $repoFormat;
    private $repoUser;

    private $passwordHasher;

    public function __construct(private EntityManagerInterface $entityManager, FormatRepository $repoFormat, UserRepository $repoUser, UserPasswordHasherInterface $passwordHasher)
    {
        $this->manager = $entityManager;
        $this->repoFormat = $repoFormat;
        $this->repoUser = $repoUser;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $countFormats = $this->formatsForm();
        echo "Format(s) créés : $countFormats\n";

        $countUsers = $this->usersCreate();
        echo "Utilisateur(s) créés : $countUsers\n";
    }

    public function formatsForm()
    {
        $dateNow = new \DateTimeImmutable();

        $arrayData = [
            "Facebook",
            "Affiche",
            "Flyer",
            "Post Instagram",
            "Post Facebook",
            "Invitation",
            "Programme évènement",
            "Logo",
            "Brochure",
            "Word",
            "Powerpoint",
        ];

        $count = 0;

        foreach ($arrayData as $formatData) {
            $format = $this->repoFormat->findOneBy(['title' => $formatData]);
            if (!$format) {
                $formatObj = new Format();
                $formatObj->setTitle($formatData);
                $formatObj->setCreatedAt($dateNow);
                $this->manager->persist($formatObj);
                $count++;
            }
        }

        $this->manager->flush();

        return $count;
    }

    public function usersCreate()
    {
        $dateNow = new \DateTimeImmutable();

        $userData = [
            [
                "username" => "Samuel ETHEVE",
                "email" => "setheve@viceversa.re",
                "password" => "setheve974",
                "roles" => ["ROLE_ADMIN"],
                "createdAt" => $dateNow,
            ],
            [
                "username" => "Mathieu Maitre",
                "email" => "mmaitre@viceversa.re",
                "password" => "mmaitre974",
                "roles" => ["ROLE_ADMIN"],
                "createdAt" => $dateNow,
            ],
            [
                "username" => "Utilisateur Admin",
                "email" => "admin@admin.com",
                "password" => "admin",
                "roles" => ["ROLE_ADMIN"],
                "createdAt" => $dateNow,
            ],
            [
                "username" => "Utilisateur User",
                "email" => "user@user.com",
                "password" => "user",
                "roles" => ["ROLE_USER"],
                "createdAt" => $dateNow,
            ],
        ];

        $count = 0;

        foreach ($userData as $userDataSingle) {
            $user = $this->repoUser->findOneBy(['email' => $userDataSingle['email']]);
            if (!$user) {
                $user = new User();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $userDataSingle['password']);
                $user->setUsername($userDataSingle['username']);
                $user->setEmail($userDataSingle['email']);
                $user->setPassword($hashedPassword);
                $user->setRoles($userDataSingle['roles']);
                $this->manager->persist($user);
                $count++;
            }
        }

        $this->manager->flush();

        return $count;
    }
}
