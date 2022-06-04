<?php

namespace App\Form;

use App\Entity\Promise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PromiseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', ChoiceType::class, [
                'choices' => [
                    '100 SAR' => '100',
                    '300 SAR' => '300',
                    '10000 SAR' => '10000'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'حفظ | Save',
                'attr' => [
                    'class' => 'btn btn-secondary'
                ]
            ])
            ->add('pay', SubmitType::class, [
                'label' => 'حفظ مع الدفع | Save and Pay',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promise::class,
        ]);
    }
}
