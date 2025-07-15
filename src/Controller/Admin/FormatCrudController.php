<?php

namespace App\Controller\Admin;

use App\Entity\Format;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FormatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Format::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityLabelInPlural('Formats')
            ->setEntityLabelInSingular('Format')
            ->setPageTitle('index', 'Gestion des Formats')
            ->setPageTitle('new', 'Créer un Format')
            ->setPageTitle('edit', 'Modifier un Format')
            ->setPageTitle('detail', 'Détail du Format');
    }

    public function createEntity(string $entityFqcn)
    {
        $dateNow = new \DateTimeImmutable('now', new \DateTimeZone('Indian/Reunion'));

        $format = new Format();
        $format
            ->setCreatedAt($dateNow)
            ;

        return $format;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Nouveau');
            })
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }


}
