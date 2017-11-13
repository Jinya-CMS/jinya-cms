<?php

namespace BackendBundle\Form;

use DataBundle\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'backend.galleries.name'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'backend.galleries.description'
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'backend.galleries.slug',
                'attr' => [
                    'placeholder' => 'generic.auto_generated'
                ]
            ])
            ->add('backgroundResource', FileType::class, [
                'required' => false,
                'label' => 'backend.galleries.background'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Gallery::class);
    }

    public function getBlockPrefix()
    {
        return 'backend_bundle_gallery_type';
    }
}
