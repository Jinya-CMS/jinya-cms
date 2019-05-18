<?php

namespace Jinya\Form\Install;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artistName', TextType::class, [
                'label' => 'install.admin.artist_name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'install.admin.email',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'install.admin.password.repeat',
                'first_options' => ['label' => 'install.admin.password'],
                'second_options' => ['label' => 'install.admin.password_repeat'],
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'install.admin.profilepicture',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', AdminData::class);
    }

    public function getBlockPrefix()
    {
        return 'install_bundle_admin_type';
    }
}
