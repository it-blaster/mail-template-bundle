<?php

namespace ItBlaster\MailTemplateBundle\Model;

use ItBlaster\MailTemplateBundle\Model\om\BaseMailTemplate;

class MailTemplate extends BaseMailTemplate
{
    public function __toString()
    {
        return $this->isNew() ? 'Новый шаблон письма' : $this->getTitleRu();;
    }

    /**
     * Заголовок на русском языке
     *
     * @return string
     */
    public function getTitleRu()
    {
        $this->setLocale('ru');
        return (string)$this->getTitle();
    }

    /**
     * Вывод языковых версий в заданном порядке
     *
     * @param null $criteria
     * @param \PropelPDO $con
     * @return array|MailTemplateI18n[]|\PropelObjectCollection
     */
    public function getMailTemplateI18ns($criteria = null, \PropelPDO $con = null)
    {
        if (class_exists('\ItBlaster\MainBundle\Helper\LiteConfig') && method_exists('\ItBlaster\MainBundle\Helper\LiteConfig', 'sortI18n')) {
            return \ItBlaster\MainBundle\Helper\LiteConfig::sortI18n(parent::getMailTemplateI18ns($criteria, $con));
        } else {
            return parent::getMailTemplateI18ns($criteria, $con);
        }
    }
}
