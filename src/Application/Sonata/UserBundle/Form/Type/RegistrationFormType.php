<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\AbstractType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder
            ->add('lastname', 'text', array('label'  => 'Nom :'))
            ->add('firstname', 'text', array('label'  => 'Prénom :'))
            ->add('company', 'text', array('label'  => 'Société :'))
            ->add('legalSituation', 'text', array('label'  => 'Statut juridique :'))
            ->add('activityType', 'choice', array(
                'choices' => array('editor' => 'Editeur Standard', 'mailer' => 'Emailer'),
                'label'  => 'Type d\'activité :'
            ))
            ->add('phoneNumber', 'text', array('label'  => 'Téléphone :'))
            ->add('url', 'url', array('label'  => 'Url :'));
    }

    public function getName()
    {
        return 'public_user_registration';
    }
}