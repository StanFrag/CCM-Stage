<?php

/**
 * Created by PhpStorm.
 * User: stanislas
 * Date: 06/05/15
 * Time: 11:48.
 */
namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder
            ->add('lastname', 'text', array('label' => 'Nom :'))
            ->add('firstname', 'text', array('label' => 'Prénom :'))
            ->add('company', 'text', array('label' => 'Nom société :'))
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
            ))
            ->add('phoneNumber', 'text', array('label' => 'Téléphone :'))
            ->add('url', 'url', array('label' => 'Url de votre site :'))
            ->setAttribute('error_type', 'inline');
    }

    public function setDefaultOption(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'validation_groups' => array('Default', 'Profile'),
            'intention' => 'profile',
        ));
    }

    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'application_sonata_user_profile';
    }
}
