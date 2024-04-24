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
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('motifRelanced', Type\ChoiceType::class, [
                'label' => 'Motive de Relance ',
                'required' => true,
                'placeholder' => '       ',
                'choices' => [
                    'Prise de Contact' => [
                        'Rendez-vous' => '1',
                        'Injoignable' => '2',

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Relanced::class,


        ]);
    }
}
