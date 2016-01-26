<?php

/**
 * Отправка шаблонных писем
 *
 * @category Symfony
 * @package ItBlasterMailTemplateBundle
 * @since 10.06.2014
 * @author Serg Yakimov <0x2f8f@gmail.com>
 */

namespace ItBlaster\MailTemplateBundle\Service;

use ItBlaster\MailTemplateBundle\Model\MailTemplateQuery;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\HttpFoundation\RequestStack;

class ItBlasterMailTemplate
{
    /** @var \Swift_Mailer Отправщик писем */
    protected $mailer;

    /** @var string E-mail отправителя */
    protected $mailer_user;

    /** @var string Имя отправителя */
    protected $mailer_user_title;

    /** @var DelegatingEngine Шаблонизатор */
    protected $templating;

    /** @var string Текущий язык */
    protected $locale;

    /** @var array Обратный адрес */
    protected $reply_to;
    /**
     * Инициализируем переменные
     *
     * @param $mailer               \Swift_Mailer
     * @param $mailer_user          string
     * @param $mailer_user_title    string
     * @param $templating           DelegatingEngine
     * @param $request_stack        RequestStack
     */
    public function __construct($mailer, $mailer_user, $mailer_user_title, $templating, $request_stack)
    {
        $request                    = $request_stack->getCurrentRequest();
        $this->mailer               = $mailer;
        $this->mailer_user          = $mailer_user;
        $this->mailer_user_title    = $mailer_user_title;
        $this->templating           = $templating;
    }

    /**
     * Swift_Mailer
     *
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * E-mail отправителя
     *
     * @return string
     */
    public function getMailerUser()
    {
        return $this->mailer_user;
    }

    /**
     * Имя отправителя
     *
     * @return string
     */
    public function getMailerUserTitle()
    {
        return $this->mailer_user_title;
    }

    /**
     * Шаблонизатор
     *
     * @return DelegatingEngine
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @return array
     */
    public function getReplyTo()
    {
        return $this->reply_to;
    }

    /**
     * @param Swift_Mailer $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $mailer_user
     */
    public function setMailerUser($mailer_user)
    {
        $this->mailer_user = $mailer_user;
    }

    /**
     * @param string $mailer_user_title
     */
    public function setMailerUserTitle($mailer_user_title)
    {
        $this->mailer_user_title = $mailer_user_title;
    }

    /**
     * @param DelegatingEngine $templating
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param array $reply_to
     */
    public function setReplyTo($reply_to)
    {
        $this->reply_to = $reply_to;
    }


    /**
     * Отправка письма
     *
     * @param string $alias_template Алиас шаблона
     * @param array $emails_to Получаетели письма
     * @param array $variables Переменные письма
     * @param string $locale Язык (ru|en)
     * @param array $attachments
     * @param string $subject
     * @return boolean
     * @throws \Exception
     */
    public function sendTemplateMail(
        $alias_template,
        array $emails_to,
        array $variables,
        $attachments = array(),
        $subject = null
    )
    {
        $mail_template = MailTemplateQuery::create()->findOneByAlias($alias_template);
        if (!$mail_template) {
            throw new \Exception('Template mail "'.$alias_template.'" not found'); //если не нашли шаблон, выкидываем исключение
        }

        //от
        $from = $this->getMailerUser();
        $from_title = $this->getMailerUserTitle();

        //заголовок письма
        if (is_null($subject)) {
            $variables['content'] = addslashes($mail_template->getTitle());
            $subject = $this->getTemplating()->render('ItBlasterMailTemplateBundle:Mail:template.html.php', $variables);
        }

        //текст письма
        $variables['content'] = addslashes($mail_template->getContent());
        $body = $this->getTemplating()->render('ItBlasterMailTemplateBundle:Mail:template.html.php', $variables);

        foreach ($emails_to as $i => $email) {
            $emails_to[$i] = trim($email);
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array(
                $from => $from_title
            ))
            ->setTo($emails_to)
            ->setBody($body)
            ->setContentType("text/html");

        if ($this->getReplyTo()) {
            $message->setReplyTo($this->getReplyTo());
        }

        if (is_array($attachments)) {
            foreach ($attachments as $attach ) {
                $message->attach(\Swift_Attachment::fromPath($attach));
            }
        }
        return $this->getMailer()->send($message);
    }

}
