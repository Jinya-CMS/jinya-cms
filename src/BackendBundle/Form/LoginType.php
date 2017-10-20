<?php

namespace BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email", EmailType::class, [
                'label' => 'account.login.label.email'
            ])
            ->add("password", PasswordType::class, [
                'label' => 'account.login.label.password'
            ])
            ->add("login", SubmitType::class, [
                'label' => 'account.login.button.login'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", LoginModel::class);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_login_form';
    }
}
