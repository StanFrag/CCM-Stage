<?php

namespace AppBundle\Admin;

use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Entity\Campaign;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\Admin;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseAdmin extends Admin{

    protected function configureListFields(ListMapper $listMapper){

        $listMapper
            ->addIdentifier('title', null, [
                'label' => 'Nom de base'
            ])
            ->add('row_count', null, [
                'label' => 'Nombre lignes'
            ])
            ->add('user', null, [
                'label' => 'Utilisateur'
            ])
            ->add('created_at', null, array(
                'label' => 'Créé le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('updated_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('campaign', null, array(
                'label' => 'Campagne associée'
            ));


        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'show' => array(),
                'edit' => array(),
                'delete' => array()
            ),
            'label' => 'Actions'
        ));

    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
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
            ->add('created_at', null, array(
                'label' => 'Créé le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('updated_at', null, array(
                'label' => 'Modifié le',
                'format' => 'd/m/Y à H\hi'
            ))
            ->add('campaign')
            ->add('base_detail')
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
        // Upload de la base
        $this->getConfigurationPool()->getContainer()->get('public_user.upload_base')->upload($base);
    }

    public function preUpdate($base) {
        // Upload de la base
        $this->getConfigurationPool()->getContainer()->get('public_user.upload_base')->update($base);
    }

    public function postPersist($base) {
        // Lors de la création d'une nouvelle Base coté Admin
        $this->populateBaseDetail($base);

        $this->sendMatching($base);
    }

    public function postRemove($base) {
        // Lors du remove d'une Base coté Admin
        $this->removeBaseDetail($base);
    }

    public function postUpdate($base) {
        $this->removePreviousBaseMatching($base);
        $this->removeBaseDetail($base);

        // Lors de l'update d'une nouvelle Base coté Admin
        $this->populateBaseDetail($base);

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine');
        $campaign = $em->getRepository('ApplicationSonataUserBundle:Campaign')->findCampaignByBase($base);

        if(null == $campaign){
            $this->sendMatching($base);
        }else{
            foreach($campaign as $targetCampaign){
                $this->removePreviousCampaignMatching($targetCampaign);
                $this->sendCampaignMatching($targetCampaign);
            }
        }
    }

    public function removePreviousBaseMatching(Base $base)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine');
        $matchs = $em->getRepository('ApplicationSonataUserBundle:Matching')->findByBase($base);

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

    public function removePreviousCampaignMatching(Campaign $campaign)
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

    protected function sendCampaignMatching($campaign){
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

    protected function sendMatching(Base $base){
        // Apres la persistance/update d'une campagne

        $idArray = array();

        // On recupere l'ensemble des bases actives
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine');
        $campaigns = $em->getRepository('ApplicationSonataUserBundle:Campaign')->findActiveCampaign();

        if(null == $campaigns){
            return;
        }else{
            // Pour chaque base on recupere son id
            foreach($campaigns as $campaign){
                $id = $campaign['id'];
                array_push($idArray, $id);
            }

            // On récupère le service qui va envoyer le match
            $sendMatching = $this->getConfigurationPool()->getContainer()->get('match_exchange_sender');
            $sendMatching->sendDB($base->getId(), $idArray, 'campaign');
        }
    }

    /**
     * @param boolean $updateAction
     * @param Base $base
     */
    protected function populateBaseDetail($base)
    {
        $file = $base->getPath();
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getEntityManager();

        // Si un fichier à été soumis durant le formulaire
        if(null !== $file){

            // On récupère le service qui va envoyer le populate
            $sendMatching = $this->getConfigurationPool()->getContainer()->get('populate_exchange_sender');
            $responsePopulate = $sendMatching->send($file, $base->getId());

            // Si le service renvoi une valeur null
            if (null !== $responsePopulate) {
                // Sinon on incremente le nombre de ligne par le nombre de ligne du fichier
                $base->setRowCount($responsePopulate);

                // Et on envoi les données
                $em->flush();
            }else{
                $em->remove($base);
                $em->flush();

                $this->setFlash('sonata_user_error', 'upload.flash.error');
                //throw new AdminException("Problème dans l'import du fichier CSV: $file, veuillez enregistrer un fichier valide");
            }
        }
    }

    protected function removeBaseDetail($base)
    {

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getEntityManager();

        $conn = $em->getConnection();

        $sth = $conn->prepare('DELETE FROM base_details WHERE fk_base = ?;');
        $sth->execute(array($base->getId()));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->set($action, $value);
    }

    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}