<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A class representing the form details of a book entity
 *
 * Class BookType
 * @package AppBundle\Form
 */
class BookType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isbn', TextType::class, [
                'label' => 'label.isbn13',
            ])
            ->add('title', TextType::class, [
                'label' => 'label.title',
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'label.summary',
                'attr'  => ['rows' => 10],
            ])
            ->add('bookCoverFile', FileType::class, [
                'label'  => 'label.cover',
                'mapped' => true,
            ])
            ->add('authors', EntityType::class, [
                'label'        => 'label.authors',
                'class'        => Author::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'attr'         => [
                    'class' => 'book-authors-select-js'
                ],
            ])
            ->add('genres', EntityType::class, [
                'label'        => 'label.genres',
                'class'        => Genre::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'attr'         => [
                    'class' => 'book-genres-select-js'
                ],
            ]);
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
