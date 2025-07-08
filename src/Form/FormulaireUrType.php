<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, EmailType, TelType, ChoiceType, DateType,
    CheckboxType, TextareaType, FileType
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;


class FormulaireUrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 🧾 IDENTITÉ
            ->add('nom_prenom', TextType::class, [
                'label' => 'Nom et prénom du demandeur',
            ])
            ->add('composante', ChoiceType::class, [
                'label' => 'Composante concernée',
                'choices' => [
                    'Web Developer' => 'web_dev',
                    'App Developer' => 'app_dev',
                    'Javascript' => 'js',
                    'React' => 'react',
                    'WordPress' => 'wp',
                    'jQuery' => 'jquery',
                    'Vue Js' => 'vue',
                    'Angular' => 'angular',
                ],
                'placeholder' => false,
                'multiple' => true,       // ✅ important
                'expanded' => false       // ✅ sinon Symfony utilise des cases à cocher
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de contact',
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('fonction', TextType::class, [
                'label' => 'Fonction / rôle',
            ])

            // 🎯 OBJECTIF
            ->add('intitule_projet', TextType::class, [
                'label' => 'Intitulé du projet / action',
            ])

            ->add('brief', TextareaType::class, [
                'label' => 'Briefing pour la création',
                'required' => true,
            ])

            // 🎯 FORMAT & SUPPORT
            ->add('formats', ChoiceType::class, [
                'label' => 'Formats souhaités',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'Affiche A3' => 'a3',
                    'Flyer A5' => 'a5',
                    'Post Instagram' => 'instagram',
                    'Post Facebook' => 'facebook',
                    'Bannière web' => 'web',
                    'Autre' => 'autre',
                ]
            ])

            ->add('supports', ChoiceType::class, [
                'label' => 'Supports de diffusion',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'Impression' => 'impression',
                    'Réseaux sociaux' => 'reseaux',
                    'Site web' => 'web',
                    'Mail / Newsletter' => 'mail',
                    'Autre' => 'autre',
                ]
            ])

            // 📎 PIÈCES JOINTES
            ->add('pieces_jointes', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => ['class' => 'dropzone', 'id' => 'my-dropzone']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
