<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 16.02.2018
 * Time: 22:39
 */

namespace Jinya\Form\Account;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'account.login.email',
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'label' => 'account.login.password',
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getBlockPrefix()
    {
        return 'account_login_type';
    }
}