<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 100    
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                        
                    ))
                ]
            ])
            ->add('last', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 100
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                        
                    ))
                ]
            ])
            ->add('country', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 100
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                        
                    ))
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 30
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true   
                    ))
                ]
                
            ])
            ->add('age', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 2
                    ]),
                    new Regex(array(
                        'pattern' => '/^[0-9]+$/',
                        'match'=> true
                        
                    ))
                   
                ]

            ])
            ->add('education', ChoiceType::class, [
                'choices' => [
                    '?????????? | Choose' => [
                        '?????????????? | Elementary School' => 'elementary school',
                        '?????????? | Middle School' => 'middle school',
                        '?????????? | High School' => 'high school',
                        '?????????????????? | Bachelor Degree' => 'bachelor degree',
                        '?????????????? | Master Degree' => 'master degree',
                        '?????????????? | PHD' => 'phd'
                        ]
                ]

            ])
            ->add('specialization', TextType::class, [

                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 100
                    ]),
                    new Regex(array(
                        'pattern' => '/^[\p{Arabic}\p{Latin}\p{N}\s*\'\-\:\ ]+$/u',
                        'match'=> true
                        
                    ))  
                ]     
            ])
            ->add('purpose', ChoiceType::class, [
                'choices' => [
                    '?????? |Yes' => 'yes',
                    '???? | No' => 'no'
                ]

            ])
            ->add('email', EmailType::class, [
                'constraints' =>[
                    new Regex(array(
                        'pattern' => '/^[a-z0-9.-]+@([a-z0-9.-]+\.)+[a-z]{2,4}$/i',
                        'match' => true
                    ))
                ]
            ])
            ->add('phone', TextType::class, [
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 11
                    ]),
                    new Regex(array(
                        'pattern' => '/^[0-9]+$/',
                        'match'=> true
                        
                    ))
                ]
            ])
            ->add('comunication', ChoiceType::class, [
                'choices' => [
                    '???????????? ???????????? ???? | WhatsApp' => 'whatsapp',
                    '???????????? ???????????????????? | Email' => 'email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Passwords don't match",
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Password', 'attr' => ['placeholder' => "Please enter 6 characters"]],
                'second_options' => ['label' => 'Confirm password', 'attr' => ['placeholder' => "Confirm password"]]

            ] )
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}