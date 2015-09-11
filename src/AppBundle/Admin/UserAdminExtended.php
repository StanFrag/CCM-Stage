<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\UserBundle\Admin\Model\UserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\ChoiceList\LegacyChoiceListAdapter;

class UserAdminExtended extends UserAdmin
{
    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$this->getSubject() || is_null($this->getSubject()->getId())) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        // avoid security field to be exported
        return array_filter(parent::getExportFields(), function ($v) {
            return !in_array($v, array('password', 'salt'));
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('company', null, array('label' => 'Societé'))
            ->add('phoneNumber', 'text', array('label' => 'Téléphone'))
            ->add('groups', 'entity', array('label' => 'Groupe(s)'))
            ->add('locked', null, array(
                'editable' => true,
                'label' => 'Verrouillé'
            ))
            ->add('createdAt', null, array(
                'label' => 'Créé le',
                'format' => 'd/m/Y',
            ))
            ->add('base', 'sonata_type_collection', array(
                'by_reference' => false,
                'label' => 'Base(s)'
            ), array(
                'edit' => 'inline',
                'inline' => 'table'
            ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('company', null, array('label' => 'Société'))
            ->add('locked', null, array('label' => 'Verrouillé'))
            ->add('phoneNumber', null, array('label' => 'Téléphone'))
            ->add('url', null, array('label' => 'Site web'))
            ->add('email', null, array('label' => 'E-mail'))
            ->add('base', null, array('label' => 'Base'))
            ->add('groups', null, array('label' => 'Groupe(s)'), null, array('multiple' => true))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('email')
            ->end()
            ->with('Groups')
                ->add('groups')
            ->end()
            ->with('Profile')
                //->add('dateOfBirth')
                ->add('firstname')
                ->add('lastname')
//                ->add('website')
//                ->add('biography')
//                ->add('gender')
//                ->add('locale')
//                ->add('timezone')
//                ->add('phone')
            ->end()
//            ->with('Social')
//                ->add('facebookUid')
//                ->add('facebookName')
//                ->add('twitterUid')
//                ->add('twitterName')
//                ->add('gplusUid')
//                ->add('gplusName')
//            ->end()
//            ->with('Security')
//                ->add('token')
//                ->add('twoStepVerificationCode')
//            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $normalUser = ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN'));

        $formMapper
            ->tab('Général')
                ->with('Utilisateur', array('class' => 'col-md-6'))
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', 'text', array(
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                    ))
                    ->add('firstname')
                    ->add('lastname')
                    ->add('company', null, array(
                            'label' => 'Société',
                    ))
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
                ->end()
                ->with('Groups', array('class' => 'col-md-6'))
                    ->add('groups', 'sonata_type_model', array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                ->end()
        ;

        if ($normalUser) {
            $formMapper
                ->with('Management', array('class' => 'col-md-6'))
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end();
        }
        $formMapper
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Rôles')
                    ->add('realRoles', 'sonata_security_roles', array(
                        'label' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ))
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
        $user->setBase($user->getBase());
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
