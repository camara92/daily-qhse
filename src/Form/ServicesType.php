<?php

namespace App\Form;

use App\Entity\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label'=> 'Titre du service', 
                'required' => true,
                'attr' => ['placeholder' => 'Saisir le titre du service proposé',
           ]

            ])
            ->add('description',
            
            TextareaType::class, [
                'label'=> 'Description', 
                'required' => true,
                'attr' => ['placeholder' => 'Détailler le service proposé',
           ]
           
           ])
            ->add('motscles', 
            
            TextType::class, [
                'label'=> 'Mots clés', 
                'required' => true,
                'attr' => ['placeholder' => 'Conseils, Orientations, formations, politique qhse, etc',
           ]
           
           ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Services::class,
        ]);
    }
}
