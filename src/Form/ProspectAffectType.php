<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Prospect;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProspectAffectType extends AbstractType
{



    public function __construct(private UserRepository $userRepository, private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->security->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_SUPER_ADMIN', $roles, true)  || in_array('ROLE_AFFECT', $roles, true) || in_array('ROLE_TEAMALL', $roles, true)) {

            $builder
                ->add('team')
                ->add('comrcl');
        } else {
            // Si l'utilisateur n'est pas SUPER_ADMIN ni AFFECT, on affiche seulement les commerciaux de son équipe
            $commerciaux = $this->userRepository->findComrclByteamOrderedByAscName($user);

            $builder
                ->add('comrcl', EntityType::class, [
                    'class' => User::class,
                    'choices' => $commerciaux,
                    'choice_label' => 'username',
                    'placeholder' => 'Sélectionnez un commercial',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,

        ]);
    }
}
