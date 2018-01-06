<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isbn', TextType::class, ['label' => 'ISBN'])
            ->add('title')
            ->add('summary', TextareaType::class, ['attr' => ['rows' => 10]])
            ->add('bookCover', FileType::class)
            ->add('authors', EntityType::class, [
                'class'        => Author::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'attr'         => [
                    'class' => 'book-authors-select-js'
                ],
            ])
            ->add('genres', EntityType::class, [
                'class'        => Genre::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'attr'         => [
                    'class' => 'book-genres-select-js'
                ],
            ]);

        $builder->get('bookCover')
            ->addModelTransformer(new CallbackTransformer(
               function($originalFile) {
                   return null;
               },
               function($submittedFile) {
                    return $submittedFile;
               }
            ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Book'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_book';
    }
}
