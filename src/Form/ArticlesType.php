<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'article ',
                'required' => true,
                'attr' => ['placeholder' => 'Entrez le titre d\'article ',
           ]] )
            ->add('description', TextareaType::class, [
                'label' => 'Desciptions',
                'required' => true,
                'attr' => ['placeholder' => 'Saisir la description',
           ] 
            ]
           
           )
            ->add('motCles'
            , TextType::class, [
                'label' => 'Ajouter des mots clés ',
                'required' => true,
                'attr' => ['placeholder' => 'Mots clés',
           ] 
            ]
            )
            ->add('categorie', TextType::class, [
                'label' => 'La catégorie de l\'article ',
                'required' => true,
                'attr' => ['placeholder' => 'Catégories d\'articles',
           ] 
            ])
            ->add('contenuPrincipal', TextareaType::class, [
                'label' => 'Contenu principal',
                'required' => true,
                'attr' => ['placeholder' => 'Ecrire le contenu principal',
           ] 
            ])
            ->add('resume', TextareaType::class, [
                'label' => 'Ajouter un resumé',
                'required' => true,
                'attr' => ['placeholder' => 'Resumer l\'article...',
           ] 
            ])
            ->add('imagesReferences', FileType::class, [
                'label' => 'Références des images ',
                'required' => true,
                'attr' => ['placeholder' => 'Ajouter des références',
           ] 
            ])
            // à chercher la méthode d'ajout d'une vidéo ou image etc .......Daouda 
            ->add('video', UrlType::class, [
                'label' => 'Lien vidéo ou ajouter la vidéo',
                'required' => true,
                'attr' => ['placeholder' => 'Url de la vidéo',
           ] 
            ])
            ->add('creditimgvideo', TextType::class, [
                'label' => 'Signé par',
                'required' => true,
                'attr' => ['placeholder' => 'signé par : Daouda , ISO 9001, Qualité et SST, SPS',
           ] 
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('updateAt', HiddenType::class, [
                // 'widget' => 'single_text',
                'mapped'=>false
            ])
            ->add('createdAt', HiddenType::class, [
                // 'widget' => 'single_text',
                'mapped'=>false
            ])
            ->add('tempslecture', IntegerType::class, [
                'label' => 'Temps de lecture estimé',
                'required' => true,
                'attr' => ['placeholder' => ' exemple : 10min',
           ] 
            ])
            ->add('user', HiddenType::class, [
                // 'class' => User::class,
                'mapped' => false,
                
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
