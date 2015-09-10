<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Sonata\UserBundle\Form\Type\ProfileType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends BaseType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'validation_groups' => array('Default', 'Profile'),
            'intention' => 'profile',
        ));
    }

    public function getName()
    {
        return 'public_user_profile';
    }
}
