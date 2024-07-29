<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Prospect;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class ProspectType extends AbstractType
{


    /**  
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Nom    (obligatoir)',
                'attr' => [

                    'placeholder' => 'Tapez le Nom du Client'
                ],
                'required' => true,
                // 'constraints' => new NotBlank(['message' => 'ne peut pas etre vide'])
            ])
            ->add('lastname', Type\TextType::class, [
                'label' => 'Prenom   (obligatoir) ',
                'attr' => [

                    'placeholder' => 'Tapez le Prénom du Client'
                ],
                'required' => false,

            ])
            ->add('phone', Type\TelType::class, [
                'label' => 'Téléphone 1    (obligatoir)',
                'required' => true,
                'constraints' => new Length([
                    'min' => 10,  'minMessage' => '  
                    le numéro de téléphone doit composer des 10 chiffres y a compris le 0 ',
                    'max' => 10, 'maxMessage' => '  
                    le numéro de téléphone doit composer des 10 chiffres y a compris le 0 '
                ]),

                'attr' => [
                    'placeholder' => 'Merci de saisir le numéro de téléphone'
                ]
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Email (obligatoir)',
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir l'adresse email"
                ]
            ])
            ->add('gender', Type\ChoiceType::class, [
                'label' => 'Genre',
                'required' => false,
                'placeholder' => '--Merci de Choisir le genre--',
                'choices' => [
                    'Male' => 1,
                    'Female' => 2
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('city', Type\TextType::class, [
                'label' => 'Ville ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ville du client',
                ]
            ])
            ->add('adress', Type\TextareaType::class, [
                'label' => 'Addresse complét (obligatoir)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Address compltét du client',
                ]
            ])
            ->add('brithAt', BirthdayType::class, [
                'label' => 'Date de Naissance ',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('source', Type\TextType::class, [
                'label' => 'Source',
                'required' => true,
                'data' => '1', // Valeur affichée  afin de persiste par default au form 
                // 'mapped' => false, // Ne pas mapper ce champ avec l'entité
                'attr' => ['readonly' => true], // Rend le champ en lecture seule
            ])
            ->add('motifSaise', Type\ChoiceType::class, [
                'label' => 'Motive de saisir ',
                'required' => true,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Parrainage' => '1',
                    'Appel Entrant' => '2',
                    'Avenant' => '3',
                    'Ancienne contrat' => '4',
                    'Propre site' => '5',
                    'Revendeur' => '6',
                    'Test' => '7',
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('typeProspect', Type\ChoiceType::class, [
                'label' => 'Type Pospect ',
                'required' => true,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Particulier' =>  '1',
                    'Professionnel' => '2',
                ],
                'expanded' => false,
                'multiple' => false,

            ])


            ->add('raisonSociale', TextType::class, [
                'label' => 'Raison sociale ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Raison sociale',
                ]
            ])
            ->add('codePost', TextType::class, [
                'label' => 'Code Postal (obligatoir)',
                'constraints' => new Length(['min' => 5,  'minMessage' => 'le code postale doit etre quatre caactaire mini', 'max' => 5, 'maxMessage' => 'le code postale doite etre 5 caractaire max']),
                'attr' => [
                    'placeholder' => 'Merci de saisir le Code Postal',
                ]
            ])
            ->add('gsm', Type\TelType::class, [
                'label' => 'Téléphone 2',
                'required' => false,
                'constraints' => new Length([
                    'min' => 10,  'minMessage' => '  
                    le numéro de téléphone doit composer des 10 chiffres y a compris le 0 ',
                    'max' => 10, 'maxMessage' => '  
                    le numéro de téléphone doit composer des 10 chiffres y a compris le 0 '
                ]),
                'attr' => [
                    'placeholder' => 'Merci de saisir si la deuxieme numéro de téléphone'
                ]
            ])
            ->add('assure', Type\ChoiceType::class, [
                'label' => 'Assuré actuellement',
                'required' => false,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('lastAssure', Type\ChoiceType::class, [
                'label' => 'Ancienne assurance résilié ',
                'required' => false,
                'placeholder' => '--Merci de selectie-- ',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add(
                'motifResil',
                Type\ChoiceType::class,
                [
                    'label' => 'Motif résiliation ',
                    'required' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'Aggravation de risque' =>  1,
                        'Amiable' =>  2,
                        'Échéance' => 3,
                        'Non-paiement' => 4,
                        'Sinistre' =>  5,
                        'Suspension de permis' => 6,
                        'Fausse déclaration' => 7
                    ],
                    'expanded' => false,
                    'multiple' => false
                ]
            )
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => 'Produit ',
                'placeholder' => '--Merci de selectie-- ',
                'choices' => $options['product_choices'],
                'multiple' => false, // Assuming `produit` is a collection
                'required' => true,

            ])

            ->add(
                'activites',
                Type\ChoiceType::class,
                [
                    'label' => 'Activites ',
                    'required' => false,
                    'placeholder' => '--Merci de selectie-- ',
                    'choices' => [
                        'TPM' =>  1,
                        'VTC' =>  2,
                        'Sociéte' => 3,
                        'Décenale' => 4,
                        'Dommage' =>  5,
                        'Marchandise' =>  6,
                        'Négociant' =>  7,
                        'Prof auto' =>  8,
                        'Garage' => 9
                    ],
                    'expanded' => false,
                    'multiple' => false,
                    // 'constraints' => [
                    //     new Callback([$this, 'validateActivites'])
                    // ]
                ]
            )
            ->add('team')
            ->add('comrcl');




        //pour reformater le numero nationnal
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $prospect = $event->getData();
                $phone = $prospect->getPhone(); // Récupération du numéro de téléphone
                $gsm = $prospect->getGsm(); // Récupération du numéro de GSM

                // Convertir le numéro de téléphone si nécessaire
                if ($phone !== null && $phone !== '') {
                    $formattedPhone = $this->convertPhoneNumberToInternational($phone);
                    if ($formattedPhone !== null && $formattedPhone !== '') {
                        $prospect->setPhone($formattedPhone);
                    }
                }

                // Convertir le numéro de GSM si nécessaire
                if ($gsm !== null && $gsm !== '') {
                    $formattedGsm = $this->convertPhoneNumberToInternational($gsm);
                    if ($formattedGsm !== null && $formattedGsm !== '') {
                        $prospect->setGsm($formattedGsm);
                    }
                }
            }
        );
    }

    /**  
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,
            // 'editing' => true,
            'product_choices' => [],

        ]);
    }

    private function convertPhoneNumberToInternational($phoneNumber)
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Vérifier la longueur du numéro pour s'assurer qu'il est valide
        if (strlen($phoneNumber) === 10) {
            // Vérifier s'il commence par '0' (format local)
            if (substr($phoneNumber, 0, 1) === '0') {
                // Remplacer le '0' par '33' pour obtenir le format international
                return '+33' . substr($phoneNumber, 1);
            }
        }
    }
}
