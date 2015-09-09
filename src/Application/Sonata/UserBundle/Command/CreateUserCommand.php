<?php

namespace Application\Sonata\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('compagny', InputArgument::REQUIRED, 'Société'),
                new InputArgument('legal-situation', InputArgument::REQUIRED, 'Situation légal'),
                new InputArgument('phone-number', InputArgument::REQUIRED, 'Numéro de téléphone'),
                new InputArgument('url', InputArgument::REQUIRED, 'Site web'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:create</info> command creates a user:

  <info>php app/console fos:user:create matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console fos:user:create matthieu matthieu@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console fos:user:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console fos:user:create thibault --inactive</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');
        $company = $input->getArgument('compagny');
        $legalSituation = $input->getArgument('legal-situation');
        $phoneNumber = $input->getArgument('phone-number');
        $url = $input->getArgument('url');
        $superadmin = $input->getOption('super-admin');

        $manipulator = $this->getContainer()->get('public_user.util.user_manipulator');
        $manipulator->create($username, $password, $email, $company, $legalSituation, $phoneNumber, $url, !$inactive, $superadmin);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }

        if (!$input->getArgument('compagny')) {
            $company = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a compagny:',
                function($company) {
                    if (empty($company)) {
                        throw new \Exception('Compagny can not be empty');
                    }

                    return $company;
                }
            );
            $input->setArgument('compagny', $company);
        }

        if (!$input->getArgument('legal-situation')) {
            $legalSituation = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a legal situation:',
                function($legalSituation) {
                    if (empty($legalSituation)) {
                        throw new \Exception('Legal situation can not be empty');
                    }

                    return $legalSituation;
                }
            );
            $input->setArgument('legal-situation', $legalSituation);
        }

        if (!$input->getArgument('phone-number')) {
            $phoneNumber = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a phone number:',
                function($phoneNumber) {
                    if (empty($phoneNumber)) {
                        throw new \Exception('Phone number can not be empty');
                    }

                    return $phoneNumber;
                }
            );
            $input->setArgument('phone-number', $phoneNumber);
        }

        if (!$input->getArgument('url')) {
            $url = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a web site:',
                function($url) {
                    if (empty($url)) {
                        throw new \Exception('Web site can not be empty');
                    }

                    return $url;
                }
            );
            $input->setArgument('url', $url);
        }
    }
}
