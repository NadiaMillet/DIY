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
            ->add('title', TextType::class)
            ->add('content', CKEditorType::class)
            ->add('categories', EntityType::class, [
                'class' => Categories::class
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

                    'label' => "Ajouter l'image de mise en avant"
                ]
            )
            ->add('img2', FileType::class, array('data_class' => null), ['label' => 'Ajouter une seconde image'])
            ->add('img3', FileType::class, array('data_class' => null), ['label' => 'Ajouter une troisième image'])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
