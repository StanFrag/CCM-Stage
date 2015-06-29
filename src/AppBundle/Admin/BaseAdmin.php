<?php

namespace AppBundle\Admin;

use Application\Sonata\UserBundle\Entity\Base;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\Admin;

class BaseAdmin extends Admin{

    protected function configureListFields(ListMapper $listMapper){
        $listMapper
            ->add('id')
            ->addIdentifier('title', null, [
                'label' => 'Nom de base'
            ])
            ->add('nb_line', null, [
                'label' => 'Nombre lignes'
            ])
            ->add('user', null, [
                'label' => 'Utilisateur'
            ])
            ->add('created_at', null, array(
                'label' => 'Créé le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('modificated_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('state', 'choice', array(
                'choices' => Base::getStateList()
            ))
            ->add('baseDetail')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('title', null, [
                'label' => 'Nom de base'
            ])
            ->add('user', null, [
                'label' => 'Utilisateur'
            ])
            ->add('state', 'doctrine_orm_string', array(), 'choice', array('choices' => Base::getStateList()))
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
            ->add('title', null, [
                'label' => 'Nom de base'
            ])
            ->add('user', 'entity', array(
                'class' => 'ApplicationSonataUserBundle:User',
                'property' => 'username',
                'label' => 'Utilisateur'
            ))
            ->add('state', 'choice', array(
                'choices' => [0 => 'Refusé', 1 => 'Accepté', 2 => 'En attente']
            ))
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'Fichier'
            ))
            ->end()
        ;
    }

    public function prePersist($base) {
        // Lors de la création d'une nouvelle Base coté Admin

        // Si un fichier à été soumis durant le formulaire
        if(null !== $this->getForm()->get('file')->getData()){

            // On récupère le service qui popule la Base de ses Bases Details
            $populate = $this->getConfigurationPool()->getContainer()->get('public_user.populate');
            $nb_line = $populate->fromCSV($this->getForm()->get('file')->getData()->getPathName(), $base);

            // Si le service renvoi une erreur
            if ($nb_line == 0) {
                $this->setFlash('sonata_user_error', 'upload.flash.error');
            }else{
                // Sinon on incremente le nombre de ligne par le nombre de ligne du fichier
                $base->setNbLine($nb_line);
                $this->setFlash('sonata_user_success', 'upload.flash.success');
            }
        }

    }

    public function preUpdate($base) {
        // Lors de l'update d'une nouvelle Base coté Admin

        // Si un fichier à été soumis durant le formulaire
        if(null !== $this->getForm()->get('file')->getData()){

            // On supprime les entités de base detail du fichier precedent
            $base->removeBaseDetailAll();

            // On récupère le service
            $populate = $this->getConfigurationPool()->getContainer()->get('public_user.populate');
            $nb_line = $populate->fromCSV($this->getForm()->get('file')->getData()->getPathName(), $base);

            // Si le service renvoi une erreur
            if ($nb_line == 0) {
                $this->setFlash('sonata_user_error', 'upload.flash.error');
            }else{
                // On update le nombre de ligne par le nombre de valeurs inserés
                $base->setNbLine($nb_line);
                $this->setFlash('sonata_user_success', 'upload.flash.success');
            }
        }
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->set($action, $value);
    }
}