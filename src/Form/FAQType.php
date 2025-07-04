<?php

namespace App\Form;

use App\Entity\FAQ;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FAQType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'label' => 'Votre question ',
                'required' => true,
                'attr' => ['placeholder' => 'Ecrivez votre question ',
           ],
               
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'Réponse ',
                'required' => true,
                'attr' => ['placeholder' => 'Apporter une réponse si vous avez  ',
           ],
               
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FAQ::class,
        ]);
    }
}
