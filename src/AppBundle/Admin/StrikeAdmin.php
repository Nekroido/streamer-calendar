<?php
/**
 * Date: 03-Sep-16
 * Time: 18:38
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Strike;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class StrikeAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        //$collection->add('add_strike', $this->getRouterIdParameter().'/strike');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $start = new \DateTime();
        $end = clone($start);
        $end->add(new \DateInterval('P5Y'));
        $formMapper
            ->add('streamer', 'sonata_type_model_autocomplete', [
                'property' => 'name'
            ])
            ->add('severity', 'choice', [
                'choices' => Strike::$choices,
                'expanded' => false,
                'multiple' => false,
                'required' => true
            ])
            ->add('reason', 'textarea')
            ->add('expires', 'sonata_type_datetime_picker', [
                'format' => 'd.MM.yyyy HH:mm',
                'dp_use_seconds' => false
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('streamer.name')
            ->add('severity')
            ->add('reason')
            ->add('expires');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('streamer.name')
            ->addIdentifier('severity', 'choice', [
                'choices' => Strike::$listChoices
            ])
            ->addIdentifier('reason')
            ->addIdentifier('expires', 'datetime', [
                'format' => 'd.m.Y H:i'
            ]);
    }
}