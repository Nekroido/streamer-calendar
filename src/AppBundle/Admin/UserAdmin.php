<?php
/**
 * Date: 01-Sep-16
 * Time: 21:49
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('add_strike', $this->getRouterIdParameter() . '/strike');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', 'text')
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('plainPassword', 'password', ['required' => false, 'property_path' => null])
            ->add('roles', 'choice', array(
                'choices' => array(
                    'User' => 'ROLE_USER',
                    'Streamer' => 'ROLE_STREAMER',
                    'Approved streamer' => 'ROLE_APPROVED_STREAMER',
                    'Manager' => 'ROLE_MANAGER',
                    'Admin' => 'ROLE_ADMIN',
                ),
                'expanded' => false,
                'multiple' => true,
                'required' => false
            ))
            ->add('personal_streamkey', 'text', ['required' => false])
            ->add('can_see_global_streamkey', 'checkbox', ['required' => false])
            ->add('donation_url', 'text', ['required' => false])
            ->add('pseudonyms', 'text', ['required' => false])
            ->add('likes', 'text', ['required' => false])
            ->add('preferredPlatforms', 'text', ['required' => false])
            ->add('about', 'textarea', ['required' => false])
            ->add('motto', 'text', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('name')
            ->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->addIdentifier('name')
            ->addIdentifier('email')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'Add strike' => array(
                        'template' => 'AppBundle:CRUD:list__action_strike.html.twig'
                    )
                )
            ));
    }

    public function preUpdate($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        if (array_key_exists('plainPassword', $formData) && $formData['plainPassword'] !== null && strlen($formData['plainPassword']) > 0) {
            $object->setPassword($this->getConfigurationPool()->getContainer()->get('security.password_encoder')->encodePassword($object, $formData['plainPassword']));
        }
    }

    public function prePersist($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        if (array_key_exists('plainPassword', $formData) && $formData['plainPassword'] !== null && strlen($formData['plainPassword']) > 0) {
            $object->setPassword($this->getConfigurationPool()->getContainer()->get('security.password_encoder')->encodePassword($object, $formData['plainPassword']));
        }
    }
}