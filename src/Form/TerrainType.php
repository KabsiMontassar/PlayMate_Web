<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\File;

class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('nomterrain')
        ->add('address')
        ->add('gouvernorat')
        ->add('gradin')
        ->add('vestiaire')
        ->add('status')
        ->add('prix', NumberType::class, [
            'label' => 'Prix',
            'invalid_message' => 'Le prix ne doit contenir que des chiffres.',
            'attr' => ['min' => 0],
        ])
        ->add('duree')
        ->add('image', FileType::class, [
            'label' => 'Image (JPG, PNG, JPEG)',
            'mapped' => false,
            'required' => false,
            'attr' => ['accept' => 'image/jpeg,image/png'],
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger une image au format JPG ou PNG.',
                ])
            ],
        ])
        ->add('video', FileType::class, [
            'label' => 'Vidéo (MP4)',
            'mapped' => false,
            'required' => false,
            'attr' => ['accept' => '.mp4'],
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'video/mp4',
                        'video/mpeg',
                        'video/quicktime',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier vidéo valide (MP4, MPEG, QuickTime).',
                ])
            ],
        ])
     ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
        ]);
    }
}
