<?php

namespace App\Form;

use App\Entity\Relanced;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RelancedType extends AbstractType
{


    /**  
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('motifRelanced', Type\ChoiceType::class, [
                'label' => 'Motif Relance ',
                'required' => false,
                'placeholder' => '       ',
                'choices' => [
                    'Prise de Contact' => [
                        'Rendez-vous' => '1',
                        'Injoignable' => '12',

                    ],
                    'Attente DOC' => '4',
                    'Tarification' => '5',
                    'Prise de Décision ' => '6',
                    'Cloture ' => [
                        'Faux Fiche' => '7',
                        'Doublon' => '8',
                        'Passage Concurrent ' => '9',
                        'Passage Contrat ' => '10',
                        'Déjà Souscrit' => '3',
                        'Test' => '11',
                        'Toujour Injoignable' => '2',
                    ],

                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['id' => 'motifRelanced']
            ])
            ->add('relacedAt', DateTimeType::class, [
                'label' => 'Date de Relance *',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d H:i')
                ]
            ])
            ->add('comment', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                'label' => "Votre Commentaire",
                'required' => false
            ]);
        // event pour redirege vers un path 
        // $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
        //     $data = $event->getData();

        //     // Vérifiez si l'option 'Passage Contrat' est sélectionnée
        //     if ($data['motifRelanced'] === '10') {
        //         // Effectuez la redirection vers le chemin 'new_client'
        //         $url = $this->urlGenerator->generate('client_new');
        //         header("Location: $url");
        //         exit();
        //     }
        // });
    }

    /**  
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Relanced::class,


        ]);
    }
}
