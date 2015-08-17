<?php

namespace AppBundle\Admin;

use Application\Sonata\UserBundle\Entity\Campaign;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            ->add('createdAt', null, array(
                'label' => 'Crée le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('modificatedAt', null, array(
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
            ->add('createdAt', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('modificatedAt', null, array(
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

    public function postPersist($campaign) {
        if($campaign->getState()){
            $this->sendMatching($campaign);
        }
    }

    public function postUpdate($campaign) {
        if($campaign->getState()){
            $this->removePreviousMatching($campaign);
            $this->sendMatching($campaign);
        }
    }

    public function removePreviousMatching(Campaign $campaign)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine');
        $matchs = $em->getRepository('ApplicationSonataUserBundle:Matching')->findByCampaign($campaign);

        if (null == $matchs) {
            return;
        }else{
            $tem = $em->getEntityManager();

            foreach($matchs as $match){
                $tem->remove($match);
            }

            $tem->flush();
        }

    }

    protected function sendMatching(Campaign $campaign){
        // Apres la persistance/update d'une campagne

        $idBases = array();

        // On recupere l'ensemble des bases actives
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine');
        $bases = $em->getRepository('ApplicationSonataUserBundle:Base')->findConsumerBases();

        if(null == $bases){
            return;
        }else{
            // Pour chaque base on recupere son id
            foreach($bases as $base){
                $id = $base['id'];
                array_push($idBases, $id);
            }

            // On récupère le service qui va envoyer le match
            $sendMatching = $this->getConfigurationPool()->getContainer()->get('match_exchange_sender');
            $sendMatching->sendDB($campaign->getId(), $idBases, 'base');
        }
    }
}
