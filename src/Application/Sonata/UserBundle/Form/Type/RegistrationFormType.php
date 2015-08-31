<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder
            ->add('username', 'text', array(
                'label' => 'Nom d\'utilisateur :',
                'attr' => array(
                    'placeholder' => 'Pseudo...',
                    'class' => 'form-control',
                ),
            ))
            ->add('email', 'email', array(
                'label' => 'Adresse e-mail :',
                'attr' => array(
                    'placeholder' => 'E-mail...',
                    'class' => 'form-control',
                ),
            ))
            ->add('lastname', 'text', array(
                'label' => 'Nom :',
                'attr' => array(
                    'placeholder' => 'Nom...',
                    'class' => 'form-control',
                ),
            ))
            ->add('firstname', 'text', array(
                'label' => 'Prénom :',
                'attr' => array(
                    'placeholder' => 'Prénom...',
                    'class' => 'form-control',
                ),
            ))
            ->add('company', 'text', array(
                'label' => 'Nom société :',
                'attr' => array(
                    'placeholder' => 'Société...',
                    'class' => 'form-control',
                ),
            ))
            ->add('legalSituation', 'choice', array(
                'choices' => array(
                                    'ei' => 'Entreprises individuelles',
                                    'sc' => 'Sociétés civiles',
                                    'eurl' => 'EURL',
                                    'sarl' => 'SARL',
                                    'sas' => 'SAS',
                                    'sa' => 'SA',
                                ),
                'label' => 'Statut juridique :',
                'placeholder' => 'Veuillez choisir...',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('activityType', 'choice', array(
                'choices' => array(
                    'editor' => 'Editeur Standard',
                    'mailer' => 'Emailer',
                ),
                'label' => 'Type d\'activité :',
                'placeholder' => 'Veuillez choisir...',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('phoneNumber', 'text', array(
                'label' => 'Téléphone :',
                'attr' => array(
                    'placeholder' => 'Téléphone...',
                    'class' => 'form-control',
                ),
            ))
            ->add('url', 'url', array(
                'label' => 'Url de votre site :',
                'attr' => array(
                    'placeholder' => 'http://www.monsite.com',
                    'class' => 'form-control',
                ),
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                    'label' => 'form.password',
                    'attr' => array(
                        'placeholder' => 'Mot de passe...',
                        'class' => 'form-control',
                    ),
                ),
                'second_options' => array(
                    'label' => 'form.password_confirmation',
                    'attr' => array(
                        'placeholder' => 'Vérification mot de passe...',
                        'class' => 'form-control',
                    ),
                ),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->setAttribute('error_type', 'inline');
    }

    public function setDefaultOption(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'validation_groups' => array('Default', 'Register'),
            'intention' => 'register',
        ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'public_user_registration';
    }
}
