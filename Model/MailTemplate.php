<?php

namespace ItBlaster\MailTemplateBundle\Model;

use ItBlaster\MailTemplateBundle\Model\om\BaseMailTemplate;

class MailTemplate extends BaseMailTemplate
{
    public function __toString()
    {
        return $this->isNew() ? 'Новый шаблон письма' : (string)$this->getTitle();
    }
}
