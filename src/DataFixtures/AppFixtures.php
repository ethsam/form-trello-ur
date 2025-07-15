<?php

namespace App\DataFixtures;

use App\Entity\Format;
use App\Repository\FormatRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppFixtures extends Fixture
{
    private $manager;
    private $repoFormat;
    private $io;

    public function __construct(private EntityManagerInterface $entityManager, FormatRepository $repoFormat)
    {
        $this->manager = $entityManager;
        $this->repoFormat = $repoFormat;
    }

    public function load(ObjectManager $manager): void
    {
        $countFormats = $this->formatsForm();
        echo "Format(s) créés : $countFormats\n";

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
}
