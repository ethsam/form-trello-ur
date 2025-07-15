<?php

namespace App\Form;

use App\Entity\Attachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType; // ✅ VichFileType au lieu de VichImageType
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichFileType::class, [
                'label' => 'Fichier :',
                'required' => false,
                'allow_delete' => false, // ❌ empêche l'affichage de la checkbox de suppression
                'download_uri' => true, // ❌ pas de lien de téléchargement automatique
                'download_label' => true, // ❌ pas de nom de fichier à côté
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attachment::class,
        ]);
    }
}
