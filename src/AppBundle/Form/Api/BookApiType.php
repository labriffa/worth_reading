<?php

namespace AppBundle\Form\Api;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

use Faker\Provider\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
class BookApiType extends AbstractType
{
    /**
     * {@inheritdoc}
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
            ->add('summary', TextType::class, [
                'label' => 'label.summary',
                'attr'  => ['rows' => 10],
            ])
            ->add('bookCoverFile', TextType::class, [
                'label'  => 'label.cover',
                'mapped' => false,
            ])
            ->add('authors', TextType::class, [
                'label'        => 'label.authors',
                'mapped'       => false
            ])
            ->add('genres', TextType::class, [
                'label'        => 'label.genres',
                'mapped'       =>  false
            ]);
    }/**
 * {@inheritdoc}
 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Book',
            'csrf_protection' => false
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
