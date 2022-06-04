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
                    'اختار | Choose' => [
                        'ابتدائي | Elementary School' => 'elementary school',
                        'متوسط | Middle School' => 'middle school',
                        'ثانوي | High School' => 'high school',
                        'بكالوريوس | Bachelor Degree' => 'bachelor degree',
                        'ماجستير | Master Degree' => 'master degree',
                        'دكتوراة | PHD' => 'phd'
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
                    'نعم |Yes' => 'yes',
                    'لا | No' => 'no'
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
                    'برنامج الواتس اب | WhatsApp' => 'whatsapp',
                    'البريد الالكتروني | Email' => 'email'
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