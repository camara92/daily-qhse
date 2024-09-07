<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('new_password', RepeatedType::class, [
                
            'type'=>PasswordType::class,
            'mapped'=>false,
            'invalid_message'=>'Le mot de passe de la confirmation doivent être identique',
            'label'=>'Mon nouveau mot de passe', 
            
            'required'=>true,
            'first_options'=>[
                'label'=>'Mon nouveau mot de passe ', 
            'attr'=>[
                'placeholder'=>'Merci de saisir votre nouveau mot de passe',
                'class'=>'form-control'
                
                ]
                
        ],
        'second_options'=>[
            'label'=>'Confirmez votre nouveau mot de passe', 
        'attr'=>[
            'placeholder'=>'Merci de saisir votre nouveau mot de passe',
            'class'=>'form-control']]
            
        ])

        // ->add('submit', SubmitType::class, [
        //     'label'=>"Mettre à jour", 
        //     'attr' => ["class"=>"btn btn-block btn-info"]
        //     ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
