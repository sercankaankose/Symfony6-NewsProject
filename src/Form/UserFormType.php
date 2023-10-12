<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('profileImage', FileType::class, [
//                'label' => 'Profile Image',
//                'mapped' => false,
//                'required' => false,
//                'attr' => [
//                    'accept' => 'image/*',
//                ],
//            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => 'readonly'
                ],
                'label_attr' => [
                    'class' => 'd-none'
                ]
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => 'readonly'
                ],
                'label_attr' => [
                    'class' => 'd-none'
                ]
            ])
            ->add('surname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => 'readonly'
                ],
                'label_attr' => [
                    'class' => 'd-none'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
