<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('last', TextType::class,
            [
                'constraints' =>
                [
                    new Length([
                        'min' => 1,
                        'max' => 50,
                    ]
                    ),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                        
                    ))
                ]
            ])
            ->add('first', TextType::class,
            [
                'constraints' =>
                [
                    new Length([
                        'min' => 1,
                        'max' => 50,
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                        
                    ))
                ]
            ])
            ->add('email', EmailType::class,
            [
                'constraints' =>
                [
                    new Regex(array(
                        'pattern' => '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i',
                        'match' => true,
                    ))
                ]
            ])
            ->add('phone')
            ->add('message', TextareaType::class,
            [
                'attr' =>
                [
                    'rows' => 7
                ],
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 3000
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\'\-\.\:\!\?\n*\t\,\;\s* ]+$/u',
                        'match'=> true
                        
                    ))
                ]

            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
