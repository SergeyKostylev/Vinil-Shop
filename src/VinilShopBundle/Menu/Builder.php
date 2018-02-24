<?php
namespace VinilShopBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class'             => 'list-group',
            )));

        $em = $this->container->get('doctrine')->getManager();

        $category = $em->getRepository('VinilShopBundle:Category')->find(22);


        $menu->addChild('Аккустические гитары', array(
            'route' => 'admin_categoryes',
            'routeParameters' => array('id' => $category->getId())

        ))->setAttribute('class', 'list-group-item');

//        $menu['Аккустические гитары']-> setLinkAttribute ('class' , 'list-group-item');
//        $menu -> setChildrenAttribute ( 'class', 'list-group-item' );
        // create another menu item
//        $menu->addChild('About Me', array('route' => 'about'));
        // you can also add sub level's to your menu's as follows
//        $menu['About Me']->addChild('Edit profile', array('route' => 'edit_profile'));


        return $menu;
    }
}
