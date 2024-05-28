<?php

namespace App\Form;


use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;


class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', Type\TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir le prénom du client'
                ]
            ])
            ->add('lastname', Type\TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir le nom du client'
                ]
            ])
            ->add('phone', Type\TextType::class, [
                'label' => 'Téléphone 2',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir le numéro de téléphone'
                ]
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => [
                    'placeholder' => "Merci de saisir l'adresse email"
                ]
            ])
            ->add('adress', Type\TextareaType::class, [
                'label' => 'Addresse complét *',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Address compltét du client',
                ]
            ])
            ->add('raisonSociale', Type\TextType::class, [
                'label' => 'Raison sociale ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Raison sociale',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
