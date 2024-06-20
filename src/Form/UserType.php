<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType

{

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }



    public function buildForm(FormBuilderInterface $builder,  array $options): void
    {



        $builder


            ->add('username', Type\TextType::class, [
                'label' => 'Username',
                'error_bubbling' => false,
                'empty_data' => '',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir Username',
                ],

            ])

            ->add('fonctions')
            ->add('roles', ChoiceType::class, [
                'choices' => [

                    'Tous' => 'ROLE_ADMIN',
                    'Chef Equipe'   => 'ROLE_TEAM',
                    'Commercial'   => 'ROLE_COMERC',
                    '*-----Prospects-----*' => [
                        'Affecter Prospects' => 'ROLE_AFFECT',
                        'Ajouter Prospect'  => 'ROLE_ADD_PROS',
                        'Traiter Prospect'    => 'ROLE_EDIT_PROS',
                    ],
                    '*-----Standard-----*' => [
                        'Gestion Standard' => 'ROLE_STAND',
                        'Ajouter Standard' => 'ROLE_ADD_STAND',
                        'Edite Standard'   => 'ROLE_EDIT_STAND',
                    ],
                    '*-----Produit-----*' => [
                        'Gestion Produit'  => 'ROLE_PROD',
                        'Ajouter Produit'  => 'ROLE_ADD_PROD',
                        'Edite Produit'  => 'ROLE_EDIT_PROD',
                    ],
                    '*-----RH-----*' => [

                        'Gestion RH' => 'ROLE_RH',
                        'Ajouter RH' => 'ROLE_ADD_RH',
                        'Edite RH' => 'ROLE_EDIT_RH',
                    ],

                    '*-----Clients-----*' => [
                        'Gestion Clients' => 'ROLE_CLIENT',
                        'Ajouter Client' => 'ROLE_ADD_CLIENT',
                        'Edite Client' => 'ROLE_EDIT_CLIENT',
                    ],
                ],



                'required' => false,
                'multiple' => true,
                'label' => 'Rôles',
                'attr' => [
                    'placeholder' => '--choisir une fonction--',
                ]
            ])

            ->add('firstname', Type\TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir le prénom'
                ]
            ])
            ->add('lastname', Type\TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir le nom'
                ]
            ])

            ->add('embuchAt', Type\DateType::class, [
                'label' => "Date d'embauche",

                'widget' => 'single_text',

            ])
            ->add('remuneration', Type\MoneyType::class, [
                'label' => 'Rémunération',
                'required' => false,
                'attr' => [

                    'placeholder' => 'Tapez   en Dhs',


                    'divisor' => 100,

                ],

            ])

            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'required' => true,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de saisir le mot de passe'
                    ],
                    'error_bubbling' => true
                ],
                'second_options' => [
                    'label' => 'Confirmez Mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de confirmez le mot de passe'
                    ],
                    'error_bubbling' => true
                ]
            ])
            ->add('gender', Type\ChoiceType::class, [
                'label' => 'Gender',

                'choices' => [
                    'Male' => 1,
                    'Female' => 0
                ],
                'expanded' => true,
                'multiple' => false
            ])

            ->add('status', Type\ChoiceType::class, [
                'label' => 'Status',

                'choices' => [
                    'Active' => 1,
                    'Desactive' => 0
                ],
                'expanded' => true,
                'multiple' => false
            ])

            ->add('products')
            ->add('teams');
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
