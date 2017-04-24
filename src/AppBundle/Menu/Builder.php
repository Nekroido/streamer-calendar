<?php
/**
 * Date: 27-Aug-16
 * Time: 17:49
 */

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav',
            ),
        ));

        $menu->addChild('Homepage', array('uri' => '/'));
        $menu['Homepage']->setLinkAttribute('target', '_top');
        $menu->addChild('Stats', array('route' => 'stats_current'));
        //$menu->addChild('Home', array('route' => 'homepage'));

        $ac = $this->container->get('security.authorization_checker');
        if ($ac->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $menu->addChild('Account', array('route' => 'user_dashboard'));

            if ($ac->isGranted('ROLE_STREAMER'))
                $menu->addChild('Information for streamers', array('route' => 'info'));

            if ($ac->isGranted('ROLE_ADMIN'))
                $menu->addChild('Control panel', array('route' => 'sonata_admin_dashboard'));

            $menu->addChild('Logout', array('route' => 'logout'));
        } else {
            $menu->addChild('Login', array('route' => 'login'));
            //if ($this->container->getParameter('invite_only') == false)
                $menu->addChild('Register', array('route' => 'user_registration'));
        }

        return $menu;
    }
}