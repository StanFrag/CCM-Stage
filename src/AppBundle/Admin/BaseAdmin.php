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
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'Fichier'
            ))
            ->end()
        ;
    }

    public function prePersist($base) {
        // Lors de la création d'une nouvelle Base coté Admin
        $this->populateBaseDetail($base);

        // Upload de la base
        $this->getConfigurationPool()->getContainer()->get('public_user.upload_base')->upload($base);
    }

    public function preUpdate($base) {
        // Lors de l'update d'une nouvelle Base coté Admin
        $this->populateBaseDetail($base, true);

        // Upload de la base
        $this->getConfigurationPool()->getContainer()->get('public_user.upload_base')->update($base);
    }

    /**
     * @param boolean $updateAction
     * @param Base $base
     */
    protected function populateBaseDetail($base, $updateAction = false)
    {
        $file = $this->getForm()->get('file')->getData();

        // Si un fichier à été soumis durant le formulaire
        if(null !== $file){

            $filePath = $this->getForm()->get('file')->getData()->getPathName();

            // S'il s'agit d'un update, on vide la base de ses baseDetails
            if($updateAction == true){
                // On supprime les entités de base detail du fichier precedent
                $base->removeBaseDetailAll();
            }

            // On récupère le service qui popule la Base de ses Bases Details
            $populate = $this->getConfigurationPool()->getContainer()->get('public_user.populate');
            $responsePopulate = $populate->fromCSV($filePath, $base);

            // Si le service renvoi une valeur null
            if (null !== $responsePopulate) {
                // Sinon on incremente le nombre de ligne par le nombre de ligne du fichier
                $base->setNbLine($responsePopulate);
            }else{
                //$this->setFlash('sonata_user_error', 'upload.flash.error');
                throw new AdminException("Problème dans l'import du fichier CSV: $file, veuillez enregistrer un fichier valide");
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