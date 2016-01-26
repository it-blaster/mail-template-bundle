<?php
namespace ItBlaster\MailTemplateBundle\Command;

use ItBlaster\MailTemplateBundle\Model\MailTemplate;
use ItBlaster\MailTemplateBundle\Model\MailTemplateQuery;
use ItBlaster\MailTemplateBundle\Service\ItBlasterMailTemplate;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Отправка письма
 *
 * Class MailListCommand
 * @package ItBlaster\MailTemplateBundle\Command
 */
class MailSendCommand extends ContainerAwareCommand
{
    /**
     * Вывод ссобщения в консоль
     *
     * @param $message
     */
    protected function log($message, $color = '')
    {
        $colors = [
            'green'     => 'info',
            'yellow'    => 'comment',
            'red'       => 'error'
        ];

        if ($color && isset($colors[$color])) {
            $message = '<'.$colors[$color].'>'.$message.'</'.$colors[$color].'>';
        }
        $this->output->writeln($message);
    }

    protected function configure()
    {
        $this
            ->setName('itblaster:mail:send')
            ->setDescription('Send mail')
//            ->addArgument('alias',InputArgument::OPTIONAL,'Template description')
            ->addOption('alias',null,InputOption::VALUE_OPTIONAL,'Алиас письма')
            ->addOption('email',null,InputOption::VALUE_OPTIONAL,'E-mail, на который будет отправлено письмо')
            ->setHelp(<<<EOF
Таск <info>%command.name%</info> отправляет тестовое письмо:

<info>php %command.full_name%</info>

--alias (алиас письма)
--email (E-mail, на который будет отправлено письмо)

<info>php %command.full_name% --alias=alias_template --email=name@mail.ru</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConsoleOutput $output */
        $this->output = $output;

        if (!($email = $input->getOption('email'))) {
            $this->log('Не указан параметр email', 'red');
            die();
        }

        if (!($alias = $input->getOption('alias'))) {
            $this->log('Не указан параметр alias', 'red');
            die();
        }

        $template =  MailTemplateQuery::create()->findOneByAlias($alias);
        if ($template) {
            /** @var ItBlasterMailTemplate $mailer */
            $mailer = $this->getContainer()->get('itblaster_mail_template');
            $result = $mailer->sendTemplateMail($alias, [$email], []);

            if ($result) {
                $this->log('Письмо успешно отправлено', 'green');
            } else {
                $this->log('Письмо не отправлено. При отправке возникли проблемы', 'red');
            }


        } else {
            $this->log('Шаблон с алиасом '.$alias.' не найден', 'red');
        }
    }
}