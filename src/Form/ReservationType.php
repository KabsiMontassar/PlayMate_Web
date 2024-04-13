<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('isconfirm')
            ->add('datereservation', DateType::class, [

                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new GreaterThan([
                        'value' => 'today',
                        'message' => 'La date doit être dans le futur.',
                    ]),
                ],
            ])
            ->add('heurereservation')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Creer un match' => 'Creer un match',
                    'postuler comme adversaire' => 'postuler comme adversaire',
                    'Lancer Un Defis' => 'Lancer Un Defis',
                ],
            ])
            ->add('idterrain');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
