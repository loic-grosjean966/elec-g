<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email *',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse email {{ value }} n\'est pas une adresse valide'
                    ]),

                    new NotBlank([
                        'message' => 'Merci de renseigner une adresse email'
                    ])
                ],
            ])

            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit faire au moins {{ limit }} caractères',
                        'max' => 55,
                        'maxMessage' => 'Votre nom est trop grand'
                    ]),

                    new NotBlank([
                        'message' => 'Veuillez renseigner un nom'
                    ])
                ]
            ])

            ->add('firstname', TextType::class, [
                'label' => 'Prénom *',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit faire au moins {{ limit }} caractères',
                        'max' => 55,
                        'maxMessage' => 'Votre prénom est trop grand'
                    ]),

                    new NotBlank([
                        'message' => 'Veuillez renseigner un prénom'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation',
                'first_options' => [
                    'label' => 'Mot de passe *',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe *',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un mot de passe'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                        'maxMessage' => 'Mot de passe trop grand'
                    ]),
                    new Regex([
                        'pattern' => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/",
                        'message' => 'Votre mot de passe doit contenir obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial'
                    ])
                ]
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les conditions d\'utilisations',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation pour continuer',
                    ]),
                ],
            ])

            // Bouton de validation
            ->add('save', SubmitType::class, [
                'label' => 'Créer mon compte',
                'attr' => [
                    'class' => 'btn btn-primary col-4 offset-4 mb-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
