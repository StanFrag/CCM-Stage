<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\Admin;

class BaseAdmin extends Admin{

    public function getTemplate($name){
        switch($name){

            /*case 'edit':
                return 'MyAppBundle::my-custom-edit.html.twig';
                break; */

            default:
                return parent::getTemplate($name);
                break;

        }

    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper
            ->add('id')
            ->addIdentifier('title')
            ->add('header')
            ->add('delimiter')
            ->add('nb_line')
            ->add('user')
            ->add('created_at')
            ->add('modificated_at')
            ->add('state', null, ['editable' => true])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('title')
            ->add('user')
            ->add('state')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper){

        $showMapper
            ->with('General')
            ->add('id')
            ->add('title')
            ->end()
        ;

    }

    /**
     * {@inheritdoc}
     */
    // Fields wich are shown in you Base Admin Tab
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('title')
            ->add('header')
            ->add('user', 'entity', array(
                'class' => 'ApplicationSonataUserBundle:User',
                'property' => 'username',
            ))
            ->add('delimiter')
            ->add('file', 'file', array('required' => true))
            ->end()
        ;
    }

    public function prePersist($base) {
        $base->upload();
    }

    public function preUpdate($base) {
        $base->upload();
    }
}