<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;

class GsmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gsm', TelType::class, [
                'label' => 'GSM 2',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Merci de saisir le deuxième numéro de téléphone'
                ]
            ]);
    }
}
