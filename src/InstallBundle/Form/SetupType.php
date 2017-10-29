<?php

namespace InstallBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('databaseHost', TextType::class, [
                'label' => 'install.database.host.label'
            ])
            ->add('databasePort', NumberType::class, [
                'label' => 'install.database.port.label'
            ])
            ->add('databaseName', TextType::class, [
                'label' => 'install.database.name.label'
            ])
            ->add('databaseUser', TextType::class, [
                'label' => 'install.database.user.label'
            ])
            ->add('databasePassword', PasswordType::class, [
                'label' => 'install.database.password.label'
            ])
            ->add('mailerTransport', TextType::class, [
                'label' => 'install.mailer.transport.label'
            ])
            ->add('mailerHost', TextType::class, [
                'label' => 'install.mailer.host.label'
            ])
            ->add('mailerUser', TextType::class, [
                'label' => 'install.mailer.user.label'
            ])
            ->add('mailerPassword', PasswordType::class, [
                'label' => 'install.mailer.password.label'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', SetupData::class);
    }

    public function getBlockPrefix()
    {
        return 'install_bundle_setup_type';
    }
}
