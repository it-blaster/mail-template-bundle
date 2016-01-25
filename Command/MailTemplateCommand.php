<?php
namespace ItBlaster\MailTemplateBundle\Command;

use ItBlaster\MailTemplateBundle\Model\MailTemplateQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailTemplateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('itblaster:mail:template')
            ->setDescription('Mail template list')
//            ->addArgument('alias',InputArgument::OPTIONAL,'Template description')
            ->addOption('alias',null,InputOption::VALUE_OPTIONAL,'Выводит информацию по шаблону')
            ->setHelp(<<<EOF
Таск <info>%command.name%</info> выводит список шаблонов писем:

<info>php %command.full_name%</info>

Так же можно указать вызвать таск с параметром --alias, указав алиаса шаблона письма, для вывода полной информации по письму

<info>php %command.full_name% --alias=alias_template</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //информация по конкретному шаблону
        if ($alias = $input->getOption('alias')) {
            $template =  MailTemplateQuery::create()->findOneByAlias($alias);
            if ($template) {
                $output->writeln('<info>'.$template->getAlias().'</info>'.'     '.$template->getTitleTemplate().'
<comment>Переменные</comment>:
'.$template->getVariables());
            } else {
                $output->writeln('<error>Шаблон с алиасом <question>'.$alias.'</question> не найден</error>');
            }

        } else { //информация по всем шаблонам
            $mail_list =  MailTemplateQuery::create()->find();
            if (count($mail_list)) {
                foreach ($mail_list as $mail) {
                    $output->writeln('<info>'.$mail->getAlias().'</info>'.'     '.$mail->getTitleTemplate());
                }
            }
            else {
                $output->writeln('нет ни одного шаблона');
            }
        }
    }
}