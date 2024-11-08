<?php

namespace App\Form;


use App\Entity\Contrat;
use App\Entity\Product;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContratType extends AbstractType
{
    public function __construct(private UserRepository $userRepository, private Security $security) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom', Type\TextType::class, [
                'label' => 'Nom ',
                'disabled' => false,
                'attr' => [

                    'placeholder' => 'Tapez le Nom du Client'
                ],
            ])
            ->add('prenom', Type\TextType::class, [
                'label' => 'Prénom ',
                'disabled' => false,
                'attr' => [

                    'placeholder' => 'Tapez le Prénom du Client'
                ],
            ])
            ->add('raisonSociale', Type\TextType::class, [
                'label' => 'Raison sociale ',
                'disabled' => false,
                'attr' => [

                    'placeholder' => 'Tapez le Raison sociale'
                ],
                'required' => false
            ])
            ->add('dateSouscrpt', Type\DateType::class, [
                'label' => "Date souscription :",
                'disabled' => false,

                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "date format: yyyy-mm-dd."
                ],
                'required' => false
            ])
            ->add('dateEffet', Type\DateType::class, [
                'label' => "Date effet :",
                'disabled' => false,

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
                    'disabled' => false,
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
                'disabled' => false,
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
                    'disabled' => false,
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
                    'disabled' => false,
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
                    'disabled' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'TPM' =>  'TPM',
                        'VTC' =>   'VTC',
                        'Sociéte' => 'Sociéte',
                        'Décenale' => 'Décenale',
                        'Négociant' =>  'Négociant',
                        'Prof auto' =>  'Prof auto',
                        'Garage' => 'Garage',
                        'AUTO ECOLE' => 'AUTO ECOLE',
                        'BTP' => 'BTP',
                        'BATIMENT' => 'BATIMENT',
                        'DEMENAGEMENT' => 'DEMENAGEMENT',
                        'TAXI' => 'TAXI',
                        'TPPC' => 'TPPC',
                        'TP' => 'TP'


                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]
            )
            ->add('imatriclt', Type\TextType::class, [
                'label' => 'Immatriculation ',
                'disabled' => false,
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
                    'disabled' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'ABADOU' =>  'ABADOU',
                        'ARCA' =>  'ARCA',
                        'NOGARO/BOETTI' => 'NOGARO/BOETTI',
                        'MFA' => 'MFA',
                        'PILLIOT' =>   'PILLIOT',
                        'TRANSASSURANCE' =>  'TRANSASSURANCE',
                        'DIF ASSURANCES' =>  'DIF ASSURANCES',
                        'ANTINEA' =>  'ANTINEA',
                        'APRIL' =>  'APRIL',
                        'ARCA ASSURANCES' =>  'ARCA ASSURANCES',
                        'ARCA TRANSPORT' =>  'ARCA TRANSPORT',
                        'ASURONE' =>  'ASURONE',
                        'ASTIER' =>  'ASTIER',
                        'CARENE' =>  'CARENE',
                        'CASSOU' =>  'CASSOU',
                        'CHAFFAULT' =>  'CHAFFAULT',
                        'DGSEINESUD' =>  'DGSEINESUD',
                        'DUBUISSON' =>  'DUBUISSON',
                        'EURODOMMAGES' =>  'EURODOMMAGES',
                        'FB ASSURANCES' =>  'FB ASSURANCES',
                        'GENERALI CHAWKI' =>  'GENERALI CHAWKI',
                        'HELVETIA' =>  'HELVETIA',
                        'MAXANCE' =>  'MAXANCE',
                        'MFA MARSEILLE' =>  'MFA MARSEILLE',
                        'MFA MONTPELLIER' =>  'MFA MONTPELLIER',
                        'NEZYS' =>  'NEZYS',
                        'PASQUIER' =>  'PASQUIER',
                        'PLESSIS' =>  'PLESSIS',
                        'PLUS SIMPLE' =>  'PLUS SIMPLE',
                        'PROGEAS' =>  'PROGEAS',
                        'SOUCHEZ' =>  'SOUCHEZ',
                        'SPRING' =>  'SPRING',
                        'SVI' =>  'SVI',
                        'TBC' =>  'TBC',
                        'TETRIS' =>  'TETRIS',
                        'TOLEDANNO' =>  'TOLEDANNO',
                        'VALEURS ASSURANCES' =>  'VALEURS ASSURANCES',
                        'WAZARI' =>  'WAZARI',
                        'ZEPHIR' =>  'ZEPHIR',
                        'AMI3F' =>  'AMI3F',
                        'NEOLIANE' =>  'NEOLIANE',
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
                    'disabled' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'ALLIANZ' =>  'ALLIANZ',
                        'AXA' =>  'AXA',
                        'MFA' => 'MFA',
                        'GENERALI' =>  'GENERALI',
                        'APRIL' => 'APRIL',
                        'AVIVA' => 'AVIVA',
                        'CARENE' => 'CARENE',
                        'EURODOMMAGES' => 'EURODOMMAGES',
                        'FB ASSURANCES' => 'FB ASSURANCES',
                        'GREAT LEAKS' => 'GREAT LEAKS',
                        'GROUPAMA' => 'GROUPAMA',
                        'HELVETIA' => 'HELVETIA',
                        'MAXANCE' => 'MAXANCE',
                        'MMA' => 'MMA',
                        'NETVOX' => 'NETVOX',
                        'PLUS SIMPLE' => 'PLUS SIMPLE',
                        'PROGEAS' => 'PROGEAS',
                        'SOLLYAZAR' => 'SOLLYAZAR',
                        'SPRING' => 'SPRING',
                        'TETRIS' => 'TETRIS',
                        'WAZARI' => 'WAZARI',
                        'ZEPHIR' => 'ZEPHIR',
                        'NOVELIA' => 'NOVELIA',
                        'DIF ASSURANCES' => 'DIF ASSURANCES',
                        'GAN' => 'GAN',
                        'MAIF' => 'MAIF',
                        'MACIF' => 'MACIF',
                        'ASSUREA' => 'ASSUREA',
                        'NEOLIANE' => 'NEOLIANE'

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
                    'disabled' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'F1' =>  'F1',
                        'F2' =>  'F2',
                        'F3' =>  'F3',
                        'TEMPORAIRE' =>  'TEMPORAIRE',
                        'F1 + VR' =>  'F1 + VR',
                        'F2 sans INCENDIE' =>  'F2 sans INCENDIE',
                        'F2 + BDG' =>  'F2 + BDG',
                        'F3 sans INCENDIE' =>  'F3 sans INCENDIE',
                        'F3 sans BDG' =>  'F3 sans BDG',
                        'F2 sans BDG' =>  'F2 sans BDG',


                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]
            )
            ->add(
                'jourPrelvm',
                Type\ChoiceType::class,
                [
                    'label' => 'Jour de prélèvement',
                    'required' => false,
                    'disabled' => false,
                    'placeholder' => '--Merci de selectie le jour du prélèvement-- ',
                    'choices' => [
                        '01' =>  '01',
                        '02' =>  '02',
                        '03 ' =>  '03 ',
                        '04 ' =>  '04',
                        '05 ' =>  '05',
                        '06 ' =>  '06',
                        '07 ' =>  '07',
                        '08 ' =>  '08',
                        '09 ' =>  '09',
                        '10 ' =>  '10',
                        '11 ' =>  '11',
                        '12 ' =>  '12',
                        '13 ' =>  '13',
                        '14 ' =>  '14',
                        '15 ' =>  '15',
                        '16 ' =>  '16',
                        '17  ' =>  '17',
                        '18  ' =>  '18',
                        '19  ' =>  '19',
                        '20  ' =>  '20',
                        '21  ' =>  '21',
                        '22  ' =>  '22',
                        '23  ' =>  '23',
                        '24  ' =>  '24',
                        '25  ' =>  '25',
                        '26  ' =>  '26',
                        '27  ' =>  '27',
                        '28  ' =>  '28',
                        '29  ' =>  '29',
                        '30  ' =>  '30',
                        '31  ' =>  '31',

                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]
            )
            ->add('datePreleveAcompte', Type\DateType::class, [
                'label' => "Date prélèvement acompte :",
                'disabled' => false,

                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "date format: yyyy-mm-dd ."
                ],
                'required' => false
            ])
            ->add('datePermis', Type\DateType::class, [
                'label' => "Date permis :",
                'disabled' => false,

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
                    'disabled' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'MENSUEL' => 'MENSUEL',
                        'TRIMESTRIEL' => 'TRIMESTRIEL',
                        'SEMESTRIEL' =>  'SEMESTRIEL',
                        'ANNUEL' =>  'ANNUEL',
                        'SEMAINE' =>  'SEMAINE',
                        '01 JRS' =>  '01 JRS',
                        '02 JRS' =>  '02 JRS',
                        '03 JRS' =>  '03 JRS',
                        '04 JRS' =>  '04 JRS',
                        '05 JRS' =>  '05 JRS',
                        '06 JRS' =>  '06 JRS',
                        '07 JRS' =>  '07 JRS',
                        '08 JRS' =>  '08 JRS',
                        '09 JRS' =>  '09 JRS',
                        '10 JRS' =>  '10 JRS',
                        '11 JRS' =>  '11 JRS',
                        '12 JRS' =>  '12 JRS',
                        '13 JRS' =>  '13 JRS',
                        '14 JRS' =>  '14 JRS',
                        '15 JRS' =>  '15 JRS',
                        '16 JRS' =>  '16 JRS',
                        '17 JRS' =>  '17 JRS',
                        '18 JRS' =>  '18 JRS',
                        '19 JRS' =>  '19 JRS',
                        '20 JRS' =>  '20 JRS',
                        '21 JRS' =>  '21 JRS',
                        '22 JRS' =>  '22 JRS',
                        '23 JRS' =>  '23 JRS',
                        '24 JRS' =>  '24 JRS',
                        '25 JRS' =>  '25 JRS',
                        '26 JRS' =>  '26 JRS',
                        '27 JRS' =>  '27 JRS',
                        '28 JRS' =>  '28 JRS',
                        '29 JRS' =>  '29 JRS',
                        '30 JRS' =>  '30 JRS',
                        '31 JRS' =>  '31 JRS',
                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]
            )
            ->add('cotisation', Type\MoneyType::class, [
                'label' => 'Cotisation  ',
                'required' => false,
                'disabled' => false,
                'currency' => false,
                'attr' => [
                    'placeholder' => 'Tapez en €',
                    'divisor' => 100,

                ],

            ])
            ->add('acompte', Type\MoneyType::class, [
                'label' => 'Acompte  ',
                'required' => false,
                'disabled' => false,
                'currency' => false,
                'attr' => [
                    'placeholder' => 'Tapez en €',
                    'divisor' => 100,

                ],

            ])
            ->add('frais', Type\MoneyType::class, [
                'label' => 'Frais  ',
                'required' => false,
                'disabled' => false,
                'currency' => false,
                'attr' => [
                    'placeholder' => 'Tapez en €',
                    'divisor' => 100,

                ],

            ])
            ->add('firstreglement', Type\MoneyType::class, [
                'label' => '1er Reglement  ',
                'required' => false,
                'disabled' => false,
                'currency' => false,
                'attr' => [
                    'placeholder' => 'Tapez en €',
                    'divisor' => 100,

                ],

            ])
            ->add('secondreglement', Type\MoneyType::class, [
                'label' => '2eme Reglement  ',
                'required' => false,
                'disabled' => false,
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
                    'disabled' => false,
                ]
            )
            ->add('typeProduct', ChoiceType::class, [
                'label' => 'Type Produit',
                'required' => true,
                'disabled' => false,
                'choices' => [
                    'AUTOMOBILE' =>  'AUTOMOBILE',
                    'CARAVANE' =>  'CARAVANE',
                    'AUTOCAR' => 'AUTOCAR',
                    'TRACTEUR ROUTIER' =>  'TRACTEUR ROUTIER',
                    'SEMI-REMORQUE' => 'SEMI-REMORQUE',
                    'POIDS LOURS' => 'POIDS LOURS',
                    'CARENE' => 'CARENE',
                    'CAMIONNETTE' => 'CAMIONNETTE',
                    'UTILITAIRE' => 'UTILITAIRE'

                ],
                'expanded' => false,
                'multiple' => false,

            ])

            ->add('status', ChoiceType::class, [
                'label' => 'status',
                'required' => true,
                'disabled' => true,
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
