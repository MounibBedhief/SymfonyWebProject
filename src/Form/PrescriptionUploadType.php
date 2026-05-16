<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PrescriptionUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prescription_pdf', FileType::class, [
                'label' => 'Ordonnance (Fichier PDF)',
                'mapped' => false, // Ce champ ne correspond pas directement à une colonne de table
                'required' => true,
                'constraints' => [
                    // Validation moderne : Symfony bloque le fichier s'il ne respecte pas ces règles
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'application/pdf',
                        ],
                        mimeTypesMessage: 'Veuillez uploader un document PDF valide.'
                    )
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Uploader',
                'attr' => ['class' => 'btn btn-primary btn-sm']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
