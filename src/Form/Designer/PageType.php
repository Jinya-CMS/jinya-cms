<?php

namespace Jinya\Form\Designer;

use Jinya\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'designer.page.title',
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'designer.page.slug',
                'attr' => [
                    'placeholder' => 'generic.auto_generated'
                ]
            ])
            ->add('content', HiddenType::class, [
                'required' => true,
                'label' => '',
                'attr' => ['data-html-input' => true]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Page::class);
    }

    public function getBlockPrefix()
    {
        return 'designer_bundle_page_type';
    }
}
