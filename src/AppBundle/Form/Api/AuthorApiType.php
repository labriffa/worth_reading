<?php

namespace AppBundle\Form\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A class representing the form details of an author entity
 *
 * Class AuthorType
 * @package AppBundle\Form
 */
class AuthorApiType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'  => 'label.name',
            ])
            ->add('biography', TextareaType::class, [
                'label'  => 'label.biography',
            ])
            ->add('avatarFile', TextType::class, [
                'label'  => 'label.avatar',
                'mapped' => false
            ])
            ->add('signatureFile', TextType::class, [
                'label'  => 'label.signature',
                'mapped' => false
            ]);
    }/**
 * {@inheritdoc}
 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Author',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_author';
    }
}
