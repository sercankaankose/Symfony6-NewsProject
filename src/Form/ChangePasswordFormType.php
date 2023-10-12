<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 5,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 15,
                        ]), new Regex([
                            'pattern' => '/^(?=.*[A-Za-z])/',
                            'message' => 'Your password must contain at least 1 letter.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*\d)/',
                            'message' => 'Your password must contain at least 1 digit.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[-_\.])/',
                            'message' => 'Your password must include at least one of -, _, or . characters.',
                        ]),
                        new Regex([
                            'pattern' => '/^[A-Za-z\d\-_\.]{5,15}$/',
                            'message' => 'Your password must be at least 5 characters long and may include -, _, or . characters.',
                        ]),
                    ],
                    'label' => 'New password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'The password fields must match.',

                'mapped' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
//           ^(?=.*[A-Za-z])(?=.*\d)(?=.*[-_\.])[A-Za-z\d\-_\.]{5,}$
