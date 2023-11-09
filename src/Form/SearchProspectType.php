<?php

namespace App\Form;

use Type\DateType;
use Type\ResetType;
use App\Entity\Team;
use App\Search\SearchProspect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SearchProspectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', Type\TextType::class, [

                'label' => "Nom :",
                'attr' => [
                    'placeholder' => "Recherche par nom du client."
                ],

                'required' => false
            ])
            ->add('m', Type\TextType::class, [

                'label' => "Prenom :",
                'attr' => [
                    'placeholder' => "Recherche par prenom du client."
                ],
                'required' => false
            ])
            ->add('g', Type\TextType::class, [
                'label' => "E-mail :",
                'attr' => [
                    'placeholder' => "Recherche par E-mail."
                ],
                'required' => false
            ])
            ->add('c', Type\TextType::class, [
                'label' => "Ville :",
                'attr' => [
                    'placeholder' => "Recherche par ville."
                ],
                'required' => false
            ])
            ->add('l', Type\TextType::class, [
                'label' => "Telephone :",
                'attr' => [
                    'placeholder' => "Recherche par numero telephone du client."
                ],
                'required' => false
            ])
            ->add('team', Type\TextType::class, [
                'label' => "Equipe :",
                'attr' => [
                    'placeholder' => "Recherche par nom d'equipe."
                ],
                'required' => false
            ])

            ->add('d', Type\DateType::class, [
                'label' => "Du :",

                'widget' => 'single_text',


                'attr' => [
                    'placeholder' => "date format: yyyy-mm-dd."
                ],
                'required' => false
            ])

            ->add('dd', Type\DateType::class, [
                'label' => "Ou :",

                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "date format: yyyy-mm-dd."
                ],
                'required' => false
            ])

            ->add('r', Type\TextType::class, [
                'label' => "Comercielle :",
                'attr' => [
                    'placeholder' => "Recherche par nom du Comercielle."
                ],
                'required' => false
            ])
            ->add('s', Type\TextType::class, [
                'label' => "r-sociale :",
                'attr' => [
                    'placeholder' => "Recherche par r-sociale."
                ],
                'required' => false
            ])

            ->add('source', Type\ChoiceType::class, [
                'label' => 'Source :',
                'required' => false,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Propre site' => 'Propre site',
                    'Saisie manuelle' => 'Saisie manuelle',
                    'Revendeur' => 'Revendeur',
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('dr', Type\DateType::class, [
                'label' => "Relance Du :",

                'widget' => 'single_text',


                'attr' => [
                    'placeholder' => "date format: yyyy-mm-dd."
                ],
                'required' => false
            ])

            ->add('ddr', Type\DateType::class, [
                'label' => "Ou :",

                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "date format: yyyy-mm-dd."
                ],
                'required' => false
            ])
            ->add('motifRelanced', Type\ChoiceType::class, [
                'label' => 'MotifRelance ',
                'required' => false,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Prise de Contact' => [
                        'Rendez-vous' => '1',
                        'Unjoing' => '2',
                        'Déjà Souscrit' => '3',
                    ],
                    'Attente Close' => '4',
                    'Tarification' => '5',
                    'Prise de Décision ' => '6',
                    'Cloture ' => [
                        'Faux Fiche' => '7',
                        'Doublon' => '8',
                        'Passage Concurrent ' => '9',
                        'Passage Contrat ' => '10',
                    ],
                ],
                'expanded' => false,
                'multiple' => false
            ]);
        // ->add('rest', Type\ResetType::class, [
        //     'label' => "Rest"
        // ]);

        // Ajoutez la validation personnalisée pour s'assurer qu'au moins un champ est rempli
        $builder->add('validate_at_least_one', Type\HiddenType::class, [
            'mapped' => false,
            'constraints' => [
                new Callback([$this, 'validateAtLeastOneField']),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchProspect::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function validateAtLeastOneField($value, ExecutionContextInterface $context)
    {
        $formData = $context->getRoot()->getData();

        // Liste des champs à vérifier
        $fieldsToCheck = ['q', 'm', 'g', 'c', 'l', 'team', 'd', 'dd', 'r', 's', 'source', 'dr', 'ddr', 'motifRelanced'];

        $fieldsFilledCount = 0;

        // Vérifiez combien de champs sont remplis
        foreach ($fieldsToCheck as $field) {
            if (!empty($formData->{$field})) {
                $fieldsFilledCount++;
            }
        }

        // Si aucun champ n'est rempli, ajoutez une violation de la contrainte
        if ($fieldsFilledCount === 0) {
            $context->buildViolation("Au moins un champ doit être rempli.")
                ->atPath('q') // Remplacez 'q' par un champ de votre choix
                ->addViolation();
        }
    }
}
