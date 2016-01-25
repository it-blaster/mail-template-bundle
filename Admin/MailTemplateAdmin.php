<?php

namespace ItBlaster\MailTemplateBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Propel1\Form\Type\TranslationCollectionType;
use Symfony\Bridge\Propel1\Form\Type\TranslationType;
use Sonata\AdminBundle\Route\RouteCollection;

class MailTemplateAdmin extends Admin
{
    public function getBreadcrumbs($action)
    {
        $breadcrumbs = parent::getBreadcrumbs($action);

        if ($action == 'edit' && $this->getRequest()->get('minimized')) {
            $breadcrumbs = array(
                array_pop($breadcrumbs)
            );
        }

        return $breadcrumbs;
    }
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('Alias', NULL, array(
                'label' => 'Алиас'
            ))
            ->add('TitleTemplate', NULL, array(
                'label' => 'Название'
            ))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('Alias', NULL, array(
                'label' => "Алиас"
            ))
            ->addIdentifier('TitleTemplate', NULL, array(
                'label' => 'Название'
            ))
            ->add('_action', 'actions', array(
                'label'    => 'Действия',
                'sortable' => FALSE,
                'actions'  => array(
                    'edit'   => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('TitleTemplate', NULL, array(
                'label'     => 'Название шаблона',
                'required'  => TRUE
            ))
            ->add('Alias', NULL, array(
                'label'     => 'Алиас',
                'required'  => TRUE
            ))
            ->add('Variables', NULL, array(
                'label'     => 'Переменные',
                'required'  => FALSE
            ))
            ->add('Title', NULL, array(
                'label'     => 'Заголовок письма',
                'required'  => true
            ))
            ->add('Content', 'ckeditor', array(
                'label'     => 'Текст письма',
                'required'  => false
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('Id', NULL, array(
                'label' => 'mail_template_id'
            ))
            ->add('Alias', NULL, array(
                'label' => 'mail_template_alias'
            ))
            ->add('Title', NULL, array(
                'label' => 'mail_template_title'
            ))
            ->add('CreatedAt', NULL, array(
                'label'  => 'mail_template_created_at',
                'format' => 'd.m.Y H:i'
            ))
            ->add('UpdatedAt', NULL, array(
                'label'  => 'mail_template_updated_at',
                'format' => 'd.m.Y H:i'
            ))
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }
}
