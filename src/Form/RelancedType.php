<?php

namespace App\Form;

use App\Entity\Relanced;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RelancedType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('motifRelanced', Type\ChoiceType::class, [
                'label' => 'Motive de Relance ',
                'required' => true,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Prise de Contact' => [
                        'Rendez-vous' => '1',
                        'Unjoing' => '2',

                    ],
                    'Attente Close' => '4',
                    'Tarification' => '5',
                    'Prise de Décision ' => '6',
                    'Cloture ' => [
                        'Faux Fiche' => '7',
                        'Doublon' => '8',
                        'Passage Concurrent ' => '9',
                        'Passage Contrat ' => '10',
                        'Déjà Souscrit' => '3',
                    ],
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('relacedAt', DateTimeType::class, [
                'label' => 'Date de Relance *',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('comment', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                'label' => "Votre Commentaire",
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Relanced::class,


        ]);
    }
}
