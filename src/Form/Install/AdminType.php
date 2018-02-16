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
            ->add('firstname', TextType::class, [
                'label' => 'backend.users.firstname',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'backend.users.lastname',
            ])
            ->add('email', EmailType::class, [
                'label' => 'backend.users.email',
            ])
            ->add('username', TextType::class, [
                'label' => 'backend.users.username',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'backend.users.password.repeat',
                'first_options' => ['label' => 'backend.users.password'],
                'second_options' => ['label' => 'backend.users.password_repeat'],
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'backend.users.profilepicture',
                'required' => false,
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
