<?php

namespace App\Form;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ValidContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                'required' => false
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
