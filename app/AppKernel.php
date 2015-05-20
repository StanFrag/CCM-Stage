<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // JMS Serializer used for media
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),

            // MEDIA
            //new Sonata\MediaBundle\SonataMediaBundle(),
            //new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),

            // Add your dependencies
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            //new Sonata\IntlBundle\SonataIntlBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // Used during installation : > php app/console sonata:easy-extends:generate SonataUserBundle -d src
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),

            // If you haven't already, add the storage bundle
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),

            // Then add SonataAdminBundle
            new Sonata\AdminBundle\SonataAdminBundle(),

            // user : Most of the cases, you'll want to extend FOSUserBundle though ;)
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),

            new CCMBenchmark\SharedanceBundle\CCMBenchmarkSharedanceBundle(),

            new AppBundle\AppBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getLogDir()
    {
        if ($this->environment === 'dev') {
            return parent::getLogDir();
        }
        return '/var/cache/sf2/md5.r-target.com/';
    }

    public function getCacheDir()
    {
        if ($this->environment === 'dev') {
            return parent::getCacheDir();
        }
        return '/var/cache/sf2/md5.r-target.com/cache/' . $this->environment;
    }
}
