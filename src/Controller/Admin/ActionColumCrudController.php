<?php

namespace App\Controller\Admin;

use App\Entity\ActionColum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ActionColumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActionColum::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $dateNow = new \DateTimeImmutable('now', new \DateTimeZone('Indian/Reunion'));

        $action = new ActionColum();
        $action
            ->setCreatedAt($dateNow)
            ->setStatus(true)
            ;

        return $action;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityLabelInPlural('Actions sur colonnes')
            ->setEntityLabelInSingular('Action sur colonne')
            ->setPageTitle('index', 'Gestion')
            ->setPageTitle('new', 'Créer')
            ->setPageTitle('edit', 'Modifier')
            ->setPageTitle('detail', 'Détail');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateField::new('createdAt', 'Crée le')->hideOnForm(),
            TextField::new('emailReceipt', 'Email de réception')->setHelp('Email de réception des notifications (ex: vincent.sumatra@univ-reunion.fr)'),
            TextField::new('titleColumn', 'Titre de la colonne')->setHelp('Titre de la colonne sur TRELLO (ex: A faire)'),
            TextField::new('idColumn', 'ID de la colonne (facultatif)')->setHelp('ID de la colonne sur TRELLO (ex: 686234ce6009f7c23fe944e7)'),
            BooleanField::new('mailForSender', 'Le demandeur reçois la notification ?'),
            BooleanField::new('status', 'Active'),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof ActionColum) {
            return;
        }

        if ($entityInstance->isMailForSender() && empty($entityInstance->getEmailReceipt())) {
            $entityInstance->setEmailReceipt('DEMANDEUR');
        }

        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof ActionColum) {
            return;
        }

        if ($entityInstance->isMailForSender() && empty($entityInstance->getEmailReceipt())) {
            $entityInstance->setEmailReceipt('DEMANDEUR');
        }

        parent::updateEntity($em, $entityInstance);
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addJsFile('assets/js/admin.js');
    }
}
