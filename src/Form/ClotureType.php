<?php

namespace App\Form;

use App\Entity\Cloture;
use App\Entity\Relanced;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClotureType extends AbstractType
{


    /**  
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('motifCloture', Type\ChoiceType::class, [
                'label' => 'Motif Cloture ',
                'required' => false,
                'placeholder' => '       ',
                'choices' => [
                    'Faux Fiche' => 'faux',
                    'Doublon' => 'doublon',
                    'Passage Concurrent ' => 'concurrent',
                    'Passage Contrat ' => 'contrat',
                    'Déjà Souscrit' => 'souscrit',
                    'Test ' => 'test',

                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['id' => 'motifRelanced']
            ])
            ->add('clotureAt', DateTimeType::class, [
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
            'data_class' => Cloture::class,


        ]);
    }
}
