<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('nom', TextType::class, [
            'label' => 'Votre nom',
            'required' => true,
            'attr' => ['placeholder' => 'Entrez votre nom',
       ],
           
        ]) 
            ->add('prenom', TextType::class, [
                'label' => 'Votre nom',
                'required' => true,
                'attr' => ['placeholder' => 'Entrez votre prénom'],
             
            ])
            ->add('photo', FileType::class, [
                'label' => 'Votre photo de profile (facultative)',
                'attr'=>['placeholder'=>'Merci de charger une image pour votre profile'],

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                'required' => false,
            ])
            ->add('adress', TextType::class, [
                'label' => 'Votre adresse complet',
                'required' => true,
                'attr' => ['placeholder' => 'pour france : 236 Rue du Pasteur 75000 Paris'],
               
            ])
            ->add('email'
            
            , TextType::class, [
                'label' => 'Votre adresse email',
                'required' => true,
                'attr' => ['placeholder' => 'Entrez votre email'],
                
            ])
           
            ->add('password', PasswordType::class, [
                'label' => 'Votre mot de passe ',
                'required' => true,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 
                'placeholder' => '123456789'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être au minimum de {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
