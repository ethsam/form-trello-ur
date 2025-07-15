<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $password = TextField::new('clearpassword')
            ->setLabel("Nouveau mot de passe")
            ->setFormTypeOption('empty_data', '')
            ->setRequired(false)
            ->setHelp('Si pas de changement, laissez le champ vide')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setColumns(12);

        $roleField = ChoiceField::new('role', 'Rôle')
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER',
            ])
            ->allowMultipleChoices(false) // important pour éviter les erreurs
            ->renderExpanded(false)       // <select> compact
            ->setRequired(true)
            ->setColumns(12);

        return [
            IdField::new('id')->onlyOnIndex(),

            FormField::addPanel('Informations')->setCssClass('col-sm-6 p-4'),
            TextField::new('username', 'Utilisateur')->setColumns(12),
            EmailField::new('email', 'Adresse e-mail')->setColumns(12),

            FormField::addPanel('Droits & Rôles')->setCssClass('col-sm-6 p-4'),
            $roleField,

            FormField::addPanel('Sécurité')->setCssClass('col-sm-6 p-4'),
            $password,
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('new', 'Nouvel utilisateur')
            ->setPageTitle('detail', 'Détail utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur');
    }
}
