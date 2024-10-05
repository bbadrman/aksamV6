<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Sav;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type as Type;

class SavType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creatAt', Type\DateType::class, [
                'label' => "Date de paiement",

                'widget' => 'single_text',

            ])
            ->add(
                'natureDemande',
                Type\ChoiceType::class,
                [
                    'label' => 'Nature Demande ',
                    'required' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'Avenant' =>  1,
                        'Remboursement' =>  2,
                        'Sinistre' => 3,
                        'Demande documents' => 4
                    ],
                    'expanded' => false,
                    'multiple' => false,
                    // 'constraints' => [
                    //     new Callback([$this, 'validateActivites'])
                    // ]
                ]
            )
            ->add('afect')
            ->add('contrat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sav::class,
        ]);
    }
}
