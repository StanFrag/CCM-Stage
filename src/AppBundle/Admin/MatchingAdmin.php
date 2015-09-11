<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class MatchingAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ->remove('delete')
            ->clearExcept(array('list', 'show'))
            ->add('downloadMatch', $this->getRouterIdParameter().'/downloadMatch')
        ;

    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('base.user', null, array(
                'label'=> 'Utilisateur'
            ))
            ->add('campaign')
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
            ->add('base')
            ->add('base.user', null, array(
                'label'=>'Utilisateur'
            ))
            ->add('campaign', null, array(
                'label'=> 'Campagne'
            ))
            ->add('match_count', null, array(
                'label'=> 'Nombre de match'
            ))
            ->add('updated_at', null, array(
                'label' => 'Effectué le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'downloadMatch' => array(
                        'template' => 'AppBundle:CRUD:list__action_download.html.twig'
                    )
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('updated_at')
            ->add('match_count')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('updated_at')
            ->add('match_count')
            ->add('matching_detail')
        ;
    }
}
