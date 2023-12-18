<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProspectClientType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('gsm', Type\TelType::class, [
                'label' => 'Téléphone ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Merci de saisir si la deuxieme numéro de téléphone'
                ]
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Email *',
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir l'adresse email"
                ]
            ])

            ->add('adress', Type\TextareaType::class, [
                'label' => 'Address Postal *',

                'attr' => [
                    'placeholder' => 'Address compltét du client',
                ]
            ]);
    }
}
