<?php

namespace App\Form;

use App\Entity\Annonces;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AnnoncesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre',
                    'class' => 'form-group'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'form-group mt-3'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Catégorie',
                    'class' => 'form-group mt-3'
                ]
            ])
            ->add(
                'img1',
                FileType::class,
                array('data_class' => null),

                [
                    'mapped' => false,
                    'required' => false,
                    // 'constraints' => [
                    //     'mimeTypes' => [
                    //         'application/png',
                    //         'application/jpeg',
                    //     ]
                    // ]

                    'label' => false
                ]
            )
            ->add('img2', FileType::class, array('data_class' => null), [
                'label' => false
            ])

            ->add('img3', FileType::class, array('data_class' => null), [
                'label' => false
            ])

            ->add('Publier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-envoyer mt-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
