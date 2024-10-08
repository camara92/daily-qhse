<?php

namespace App\Form;

use App\Entity\References;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          

           

            
                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'required' => true,
                    'attr' => ['placeholder' => 'Entrez votre le nom de votre référence'],
                 
                ])
                ->add('prenom', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true,
                    'attr' => ['placeholder' => 'Entrez votre le prénom de votre référence'],
                 
                ])
                ->add('societe', TextType::class, [
                    'label' => 'Nom de la société',
                    'required' => true,
                    'attr' => ['placeholder' => 'Entrez le nom de la société'],
                 
                ])
                ->add('mission', TextType::class, [
                    'label' => 'Décrire vos missions',
                    'required' => true,
                    'attr' => ['placeholder' => 'Décrire brièvement vos missions'],
                 
                ])
                ->add('photo', FileType::class, [
                    'label' => 'Photo',
                    'attr'=>['placeholder'=>'Merci de charger une image pour votre référence'],
    
                    'mapped' => false,
    
                    'required' => false,
                ])
              
               
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => References::class,
        ]);
    }
}
