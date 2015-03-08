<?php

namespace Millennium\PaginationBundle\Twig;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Extension extends \Twig_Extension
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * 
     * @param ContainerInterface $container
     * @param TwigEngine $templating
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('pagination', array($this, 'results')),
            new \Twig_SimpleFilter('navigation', array($this, 'navigation'))
        );
    }

    public function results($pagination)
    {
        return $pagination['results'];
    }

    public function navigation($paginator, $settings = array())
    {
        
        $options = $this->container->getParameter('millennium.pagination.template');
        $paginator['classes'] = $this->container->getParameter('millennium.pagination.classes');
        return $this->container->get('templating')->render($options['pagination'], $paginator);
    }

    public function getName()
    {
        return 'millennium_pagination_extension';
    }

}
