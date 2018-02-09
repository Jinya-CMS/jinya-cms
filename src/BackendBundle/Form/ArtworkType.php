<?php

namespace BackendBundle\Form;

use DataBundle\Entity\Artwork;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'backend.artworks.name'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'backend.artworks.description'
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'backend.artworks.slug',
                'attr' => [
                    'placeholder' => 'generic.auto_generated'
                ]
            ])
            ->add('pictureResource', FileType::class, [
                'required' => false,
                'label' => 'backend.artworks.picture'
            ])
            ->add('labelsChoice', ChoiceType::class, [
                'label' => 'backend.artworks.labels',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choice_label' => 'name',
                'choices' => $options['all_labels'],
                'choice_value' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Artwork::class);
        $resolver->setRequired('all_labels');
    }

    public function getBlockPrefix()
    {
        return 'backend_bundle_artwork_type';
    }
}
