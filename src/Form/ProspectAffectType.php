<?php

namespace App\Form;

use App\Entity\Prospect;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProspectAffectType extends AbstractType
{



    public function __construct(private UserRepository $userRepository, private Security $security)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('team')
            ->add('comrcl');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,

        ]);
    }
}
