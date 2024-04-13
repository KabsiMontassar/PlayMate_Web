<?php

namespace App\Form;

use App\Entity\Tournoi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;



class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nbmaxequipe', ChoiceType::class, [
            'choices' => [
                '4' => '4',
                '8' => '8',
                '16 ' => '16',
                '24' => '24',
                '32' => '32',
            ],
            'data' => '8', // Set Option A as the default choice
            'label' => false,
            'expanded' => false,
            'multiple' => false,
            'attr' => ['style' => 'display:none;'],
        ])
            ->add('nom', TextType::class)
            ->add('affiche', FileType::class, [
                'mapped' => false, 
                'required' => false,
             
                
            ])
            ->add('address')
        
            ->add('datedebut', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',])
            ->add('datefin', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',])
            ->add('idorganisateur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
