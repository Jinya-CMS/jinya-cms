<?php

namespace Jinya\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
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
            ]);
        if ($builder->getData() instanceof AddUserData) {
            $builder->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'backend.users.password.repeat',
                'first_options' => ['label' => 'backend.users.password'],
                'second_options' => ['label' => 'backend.users.password_repeat'],
            ]);
        }
        $builder
            ->add('profilePicture', FileType::class, [
                'label' => 'backend.users.profilepicture',
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'backend.users.active',
                'required' => false,
            ])
            ->add('superadmin', CheckboxType::class, [
                'label' => 'backend.users.superadmin',
                'required' => false,
            ])
            ->add('admin', CheckboxType::class, [
                'label' => 'backend.users.admin',
                'required' => false,
            ])
            ->add('writer', CheckboxType::class, [
                'label' => 'backend.users.editor',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', UserData::class);
    }

    public function getBlockPrefix()
    {
        return 'backend_bundle_user_type';
    }
}
