<?php

namespace App\Form;

use App\Entity\Opinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

/**
 * Classe permettant de créer le formulaire pour publier des avis
 */
class OpinionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'=> 'Titre',
                'attr' => [
                    'placeholder' => 'Que pensez-vous d\'Elec-G ?'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre titre doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Votre titre doit contenir au maximum {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('mark', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Ici vous pouvez dévelloper votre point de vue'
                ],
                'purify_html' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un contenu'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères',
                        'max' => 10000,
                        'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères'
                    ]),
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn btn-primary col-sm-4 offset-sm-4 col-md-2 offset-md-5'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Opinion::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
