<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 15:57
 */

namespace App\Command;


use App\Entity\Merchant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CreateUserCommand extends Command
{
    private $encoderFactory;
    private $em;

    public function __construct(EncoderFactoryInterface $encoderFactory, EntityManagerInterface $em)
    {
        parent::__construct("app:create:user");
        $this->encoderFactory = $encoderFactory;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $question = new Question("Email: ");
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper("question");

        $email = $helper->ask($input, $output, $question);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new \DomainException("Invalid email!");

        $passwordQuestion = new Question("Password: ");
        $passwordQuestion->setHidden(true);

        $password1 = $helper->ask($input, $output, $passwordQuestion);

        $passwordQuestion = new Question("Repeat password: ");
        $passwordQuestion->setHidden(true);
        $password2 = $helper->ask($input, $output, $passwordQuestion);

        if ($password1 !== $password2)
            throw new \DomainException("Passwords not identical!");

        $user = new Merchant($email, $this->encoderFactory, $password1);
        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("User created!");
    }

}