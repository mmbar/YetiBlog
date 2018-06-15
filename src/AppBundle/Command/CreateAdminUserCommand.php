<?php


namespace AppBundle\Command;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateAdminUserCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:create-admin-user')
            ->setDescription('Creates a new admin user.')
            ->setHelp('This command allows you to create a admin user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln([
            'Admin User Creator',
            '==================',
            '',
        ]);

        $helper = $this->getHelper('question');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository(User::class);

        $askFor = function ($property) use ($input, $output, $helper,$repository){
            $question = new Question('Please enter the user: '.$property.' : ');
            if ($property == 'password') {
                $question->setHidden(true);
            }
            $question->setValidator(function ($value ) use ($property,$repository) {
                if (empty($value)) {
                    throw new \Exception('User: '.$property.' can not be empty');
                }
                if ($property != 'password' && $repository->findOneBy([$property => $value])){
                    throw new \Exception($property.' already used');
                }
                return $value;
            });

            return $helper->ask($input,$output,$question);
        };

        $user = new User();
        $user->setUsername($askFor('username'));
        $user->setEmail($askFor('email'));
        $user->setPlainPassword($askFor('password'));
        $user->addRole('ROLE_ADMIN');

        $validator = $this->getContainer()->get('validator');
        $errors = $validator->validate($user);
        if (count($errors)> 0){
            $errorsString = (string) $errors;
            $output->writeln($errorsString);
            return;
        }

        $em->persist($user);
        $em->flush();

        $output->writeln('User created!');

    }

}