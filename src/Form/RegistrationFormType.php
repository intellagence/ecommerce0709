<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Email<span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => [
                    'placeholder' => 'Saisir un email',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un email'
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir un email correct'
                    ])
                ]
            ])

            ->add('lastName', null, [
                'label' => 'Nom<span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => [
                    'placeholder' => 'Saisir le nom',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom'
                    ])
                ]
            ])

            ->add('firstName', null, [
                'label' => 'Prénom<span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => [
                    'placeholder' => 'Saisir le prénom',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le prénom'
                    ])
                ]
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'label' => '<a href="">J\'accepte les CGU</a>',
                'label_html' => true,
                'mapped' => false, // n'est pas une propriété de l'entity
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_?.])([-+!*$@%_?.\w]{12,})$/',
                            'match' => true,
                            'message' => 'Votre mot de passe doit contenir une minuscule, une majuscule, un chiffre, un caractère spécial parmi les suivants : -+!*$@%_?. (12 caractères minimum) '
                        ])
                    ],
                ],
                'mapped' => false,
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe<span class="text-danger">*</span>',
                    'label_html' => true,
                    'attr' => [
                        'placeholder' => 'Saisir le mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe<span class="text-danger">*</span>',
                    'label_html' => true,
                    'attr' => [
                        'placeholder' => 'Confirmer le mot de passe',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
