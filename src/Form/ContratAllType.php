<?php

namespace App\Form;


use App\Entity\Contrat;
use App\Entity\Product;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContratAllType extends AbstractType
{
    public function __construct(private UserRepository $userRepository, private Security $security) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_VALIDE', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
            $builder
                ->add('nom', Type\TextType::class, [
                    'label' => 'Nom ',
                    'disabled' => true,
                    'attr' => [

                        'placeholder' => 'Tapez le Nom du Client'
                    ],
                ])
                ->add('prenom', Type\TextType::class, [
                    'label' => 'Prénom ',
                    'disabled' => true,
                    'attr' => [

                        'placeholder' => 'Tapez le Prénom du Client'
                    ],
                ])
                ->add('raisonSociale', Type\TextType::class, [
                    'label' => 'Raison sociale ',
                    'disabled' => true,
                    'attr' => [

                        'placeholder' => 'Tapez le Raison sociale'
                    ],
                    'required' => false
                ])
                ->add('dateSouscrpt', Type\DateType::class, [
                    'label' => "Date souscription :",
                    'disabled' => true,

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add('dateEffet', Type\DateType::class, [
                    'label' => "Date effet :",
                    'disabled' => true,

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add(
                    'etat',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Etat Contrat ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'EN-COURS' =>   'EN-COURS',
                            'SUSPENDU' =>   'SUSPENDU',
                            'ANNULE' =>   'ANNULE',
                            'RESILIE' =>   'RESILIE'


                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )

                ->add('conducteur', Type\TextType::class, [
                    'label' => 'Conducteur ',
                    'disabled' => true,
                    'required' => false,
                    'attr' => [

                        'placeholder' => 'Tapez le Nom du conducteur'
                    ],
                ])
                ->add(
                    'typeConducteur',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Type Conducteur ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'Désigné' =>  'Désigné',
                            'Multiconducteur' => 'Multiconducteur',

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'type',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Type Contrat ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'Particulier' =>  'Particulier',
                            'Professionnel' => 'Professionnel',

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'activite',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Activites ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'TPM' =>  'TPM',
                            'VTC' =>   'VTC',
                            'Sociéte' => 'Sociéte',
                            'Décenale' => 'Décenale',
                            'Dommage' =>   'Dommage',
                            'Marchandise' =>   'Marchandise',
                            'Négociant' =>  'Négociant',
                            'Prof auto' =>  'Prof auto',
                            'Garage' => 'Garage'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add('imatriclt', Type\TextType::class, [
                    'label' => 'Immatriculation ',
                    'disabled' => true,
                    'attr' => [

                        'placeholder' => 'Tapez Immatriculation'
                    ],
                    'required' => false
                ])
                ->add(
                    'partenaire',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Partenaire ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'ABADOU' =>  'ABADOU',
                            'ARCA' =>  'ARCA',
                            'NOGARO/BOETTI' => 'NOGARO/BOETTI',
                            'MFA' => 'MFA',
                            'PILLIOT' =>   'PILLIOT',
                            'TRANSASSURANCE' =>  'TRANSASSURANCE',
                            'DIF ASSURANCES' =>  'DIF ASSURANCES'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'compagnie',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Compagnie ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'ALLIANZ' =>  'ALLIANZ',
                            'AXA' =>  'AXA',
                            'MFA' => 'MFA',
                            'GENERALI' =>  'GENERALI'

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'formule',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Formule ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'F1' =>  'F1',
                            'F2' =>  'F2',
                            'F3' =>  'F3'

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add('datePrelvm', Type\DateType::class, [
                    'label' => "Date prélèvement :",
                    'disabled' => true,

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add('datePreleveAcompte', Type\DateType::class, [
                    'label' => "Date prélèvement acompte :",
                    'disabled' => true,

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd ."
                    ],
                    'required' => false
                ])
                ->add('datePermis', Type\DateType::class, [
                    'label' => "Date permis :",
                    'disabled' => true,

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add(
                    'fraction',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Fractionnement ',
                        'required' => false,
                        'disabled' => true,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'MENSUEL' => 'MENSUEL',
                            'TRIMESTRIEL' => 'TRIMESTRIEL',
                            'SEMESTRIEL' =>  'SEMESTRIEL',
                            'ANNUEL' =>  'ANNUEL'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add('cotisation', Type\MoneyType::class, [
                    'label' => 'Cotisation  ',
                    'required' => false,
                    'disabled' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('acompte', Type\MoneyType::class, [
                    'label' => 'Acompte  ',
                    'required' => false,
                    'disabled' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('frais', Type\MoneyType::class, [
                    'label' => 'Frais  ',
                    'required' => false,
                    'disabled' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('firstreglement', Type\MoneyType::class, [
                    'label' => '1er Reglement  ',
                    'required' => false,
                    'disabled' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('secondreglement', Type\MoneyType::class, [
                    'label' => '2eme Reglement  ',
                    'required' => false,
                    'disabled' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])

                ->add(
                    'products',
                    EntityType::class,
                    [
                        'class' => Product::class,
                        'placeholder' => 'Sélectionnez un Produit',
                        'disabled' => true,
                    ]
                )

                ->add('status', ChoiceType::class, [
                    'label' => 'status',
                    'required' => true,
                    'choices' => [
                        'Valider' => 1,
                        'Rejeter' => 2
                    ],

                    'expanded' => true,
                    'multiple' => false
                ])

                ->add('comment', TextareaType::class, [
                    'attr' => ['class' => 'tinymce'],
                    'label' => "Votre Commentaire",
                    'required' => false,
                ]);
        } else {
            $builder
                ->add('nom', Type\TextType::class, [
                    'label' => 'Nom ',
                    'attr' => [

                        'placeholder' => 'Tapez le Nom du Client'
                    ],
                ])
                ->add('prenom', Type\TextType::class, [
                    'label' => 'Prénom ',
                    'attr' => [

                        'placeholder' => 'Tapez le Prénom du Client'
                    ],
                ])
                ->add('raisonSociale', Type\TextType::class, [
                    'label' => 'Raison sociale ',
                    'attr' => [

                        'placeholder' => 'Tapez le Raison sociale'
                    ],
                    'required' => false
                ])
                ->add('dateSouscrpt', Type\DateType::class, [
                    'label' => "Date souscription  :",

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add('dateEffet', Type\DateType::class, [
                    'label' => "Date effet :",

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add(
                    'etat',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Etat Contrat ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'EN-COURS' =>   'EN-COURS',
                            'SUSPENDU' =>   'SUSPENDU',
                            'ANNULE' =>   'ANNULE',
                            'RESILIE' =>   'RESILIE'


                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )

                ->add('conducteur', Type\TextType::class, [
                    'label' => 'Conducteur ',
                    'required' => false,
                    'attr' => [

                        'placeholder' => 'Tapez le Nom du conducteur'
                    ],
                ])
                ->add(
                    'typeConducteur',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Type Conducteur ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'Désigné' =>  'Désigné',
                            'Multiconducteur' => 'Multiconducteur',

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'type',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Type Contrat ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'Particulier' =>  'Particulier',
                            'Professionnel' => 'Professionnel',

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'activite',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Activites ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'TPM' =>  'TPM',
                            'VTC' =>   'VTC',
                            'Sociéte' => 'Sociéte',
                            'Décenale' => 'Décenale',
                            'Dommage' =>   'Dommage',
                            'Marchandise' =>   'Marchandise',
                            'Négociant' =>  'Négociant',
                            'Prof auto' =>  'Prof auto',
                            'Garage' => 'Garage'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add('imatriclt', Type\TextType::class, [
                    'label' => 'Immatriculation ',
                    'attr' => [

                        'placeholder' => 'Tapez Immatriculation'
                    ],
                    'required' => false
                ])
                ->add(
                    'partenaire',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Partenaire ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'ABADOU' =>  'ABADOU',
                            'ARCA' =>  'ARCA',
                            'NOGARO/BOETTI' => 'NOGARO/BOETTI',
                            'MFA' => 'MFA',
                            'PILLIOT' =>   'PILLIOT',
                            'TRANSASSURANCE' =>  'TRANSASSURANCE',
                            'DIF ASSURANCES' =>  'DIF ASSURANCES'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'compagnie',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Compagnie ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'ALLIANZ' =>  'ALLIANZ',
                            'AXA' =>  'AXA',
                            'MFA' => 'MFA',
                            'GENERALI' =>  'GENERALI'

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add(
                    'formule',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Formule ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'F1' =>  'F1',
                            'F2' =>  'F2',
                            'F3' =>  'F3'

                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add('datePrelvm', Type\DateType::class, [
                    'label' => "Date prélèvement :",

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add('datePreleveAcompte', Type\DateType::class, [
                    'label' => "Date prélèvement acompte :",

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd ."
                    ],
                    'required' => false
                ])
                ->add('datePermis', Type\DateType::class, [
                    'label' => "Date permis :",

                    'widget' => 'single_text',
                    'attr' => [
                        'placeholder' => "date format: yyyy-mm-dd."
                    ],
                    'required' => false
                ])
                ->add(
                    'fraction',
                    Type\ChoiceType::class,
                    [
                        'label' => 'Fractionnement ',
                        'required' => false,
                        'placeholder' => '--Merci de selectie-- ',
                        'choices' => [
                            'MENSUEL' => 'MENSUEL',
                            'TRIMESTRIEL' => 'TRIMESTRIEL',
                            'SEMESTRIEL' =>  'SEMESTRIEL',
                            'ANNUEL' =>  'ANNUEL'
                        ],
                        'expanded' => false,
                        'multiple' => false,
                    ]
                )
                ->add('cotisation', Type\MoneyType::class, [
                    'label' => 'Cotisation  ',
                    'required' => false,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('acompte', Type\MoneyType::class, [
                    'label' => 'Acompte  ',
                    'required' => false,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('frais', Type\MoneyType::class, [
                    'label' => 'Frais  ',
                    'required' => false,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('firstreglement', Type\MoneyType::class, [
                    'label' => '1er Reglement  ',
                    'required' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])
                ->add('secondreglement', Type\MoneyType::class, [
                    'label' => '2eme Reglement  ',
                    'required' => false,
                    'disabled' => true,
                    'currency' => false,
                    'attr' => [
                        'placeholder' => 'Tapez en €',
                        'divisor' => 100,

                    ],

                ])

                ->add(
                    'products',
                    EntityType::class,
                    [
                        'class' => Product::class,
                        'placeholder' => 'Sélectionnez un Produit',

                    ]
                )



                ->add('comment', TextareaType::class, [
                    'attr' => ['class' => 'tinymce'],
                    'label' => "Remarque",
                    'required' => false,
                    'disabled' => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
