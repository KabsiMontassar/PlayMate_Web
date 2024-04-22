<?php

namespace App\Form;

use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//range type 
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomequipe')
            ->add('nbrejoueur', RangeType::class, [
                'attr' => [
                    'min' => 6,
                    'max' => 8,
                    'step' => 1
                ]
                
                ])
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
