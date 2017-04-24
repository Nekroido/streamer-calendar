<?php
/**
 * Date: 01-Sep-16
 * Time: 21:59
 */

namespace AppBundle\Admin;

use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class TokenAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit');

        $collection->add('generate');
    }

    protected function configureTabMenu(ItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $menu->addChild('Generate', [
            'route' => 'admin_app_token_generate',
            'attributes' => [
                'icon' => 'glyphicon glyphicon-flash'
            ]
        ]);
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('token', null, [
                'template' => 'AppBundle:CRUD:registration_url_view.html.twig',
                'label' => 'Link for registration'
            ])
            //->add('token', 'text')
            ->add('usedBy.name', 'text')
            ->add('usedAt', 'date')
            ->add('isUsed', 'boolean');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('token')
            ->add('usedBy.name')
            ->add('isUsed')
            ->add('usedAt');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('token')
            ->addIdentifier('usedBy.name')
            ->addIdentifier('isUsed')
            ->addIdentifier('usedAt');
    }
}