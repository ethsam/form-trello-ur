<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Form\AttachmentType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $nowDateTime = new \DateTimeImmutable();

        return [
            IdField::new('id')->hideOnForm(),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm()->hideOnIndex(),
            DateTimeField::new('updatedAt', 'Mise à jour le')->hideOnForm()->hideOnIndex(),

            FormField::addPanel('Contenu')->setCssClass('col-sm-9 p-4'),
            DateTimeField::new('dateEvent', 'Date de l\'événement')->hideOnForm()->hideOnIndex(),
            DateTimeField::new('dateLimit', 'Date limite de réception')->hideOnForm()->hideOnIndex(),
            TextField::new('title', 'Titre')->setColumns(12),
            TextEditorField::new('brief', 'Briefing')->setColumns(12)->hideOnIndex(),
            TextField::new('composante', 'Composante')->setColumns(12),
            TextField::new('link', 'Lien')->setColumns(12)->hideOnIndex(),

            CollectionField::new('attachments')->setEntryType(AttachmentType::class)
                                            ->setFormTypeOption('by_reference', false)
                                            ->setRequired(false)
                                            ->hideOnIndex()
                                            ->setTranslationParameters([
                                                'form.label.delete'=>'Supprimer du serveur',
                                                'action.choose_file'=>'Envoyer un fichier'
                                            ]),

            AssociationField::new('formats', 'Formats')
                ->setColumns(12)
                ->hideOnIndex()
                ->setFormTypeOption('by_reference', false) // Important pour les relations ManyToMany
                ->setRequired(false)
                ->setColumns(12),
                //->autocomplete(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityLabelInPlural('Demande')
            ->setEntityLabelInSingular('Demandes')
            ->setPageTitle('index', 'Gestion des Demandes')
            ->setPageTitle('edit', 'Edit une Demande');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW); // ❌ désactive le bouton "Créer"
    }
}
