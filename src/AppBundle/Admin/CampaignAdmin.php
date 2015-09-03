<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CampaignAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, [
                'label' => 'Nom de la campagne'
            ])
            ->add('description', null, [
                'label' => 'Description'
            ])
            ->add('state', 'doctrine_orm_string', array(), 'choice', array('choices' => array(0 => 'Fermée', 1 => 'En cours')))
            ->add('base')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->add('id')
            ->add('title', null, [
                'label' => 'Titre'
            ])
            ->add('description')
            ->add('created_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('modificated_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('state', 'choice', array(
                'choices' => array(0 => 'Fermée', 1 => 'En cours'),
                'label' => 'Etat'
            ))
            ->add('base')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
            ->add('img', null, array(
                'template' => 'ApplicationSonataUserBundle:Admin:image_show.html.twig',
                'label' => 'Image'
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, [
                'label' => 'Nom de la campagne'
            ])
            ->add('description', 'textarea', [
                'label' => 'Description'
            ])
            ->add('img', 'file', [
                'label' => 'Image',
                'required' => false
            ])
            ->add('base')
            ->add('state', 'choice', array(
                'choices' => array(0 => 'Fermée', 1 => 'En cours'),
                'label' => 'Etat de la campagne'
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title', null, [
                'label' => 'Nom de la campagne'
            ])
            ->add('description')
            ->add('created_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('modificated_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('state', 'choice', array(
                'choices' => array(0 => 'Fermée', 1 => 'En cours'),
                'label' => 'Etat'
            ))
            ->add('base')
            ->add('img', null, array(
                'template' => 'ApplicationSonataUserBundle:Admin:image_show.html.twig',
                'label' => 'Image'
            ))
        ;
    }
}
