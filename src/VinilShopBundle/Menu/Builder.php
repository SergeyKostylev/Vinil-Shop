<?php
namespace VinilShopBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use VinilShopBundle\Entity\Category;


class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class'             => 'list-group li-marker-none',
            )));

        $linkClasses = 'list-group-item btn btn-secondary link-main-menu-style';



        $em = $this->container->get('doctrine')->getManager();
        $menu->addChild('<i class="fas fa-home"></i> В начало', [
                'route' => 'home_page',
                'extras' => array('safe_label' => true)
            ])
            ->setLinkAttribute('class', $linkClasses)


        ;



        $categoryes = $em->getRepository('VinilShopBundle:Category')->firstParentCategories();
        /**
         * @var Category $category
         */
        foreach ($categoryes as $category){
            $childrenAmount = count($category->getChildren());
            $parameters =
                $childrenAmount ?
                ['route' => 'children_categotyes', 'routeParameters' => ['id' => $category->getId() ] ] :
                ['route' => 'products_by_category', 'routeParameters' => ['id' => $category->getId() ]];
            $menu
                ->addChild($category->getName(),$parameters)
                ->setLinkAttribute('class', $linkClasses)
            ;
        }




        return $menu;
    }
}
