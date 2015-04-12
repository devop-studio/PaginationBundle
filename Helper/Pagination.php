<?php

namespace Millennium\PaginationBundle\Helper;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Pagination
{

    /**
     *
     * @var Router
     */
    private $router;

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @var TwigEngine
     */
    private $templating;

    /**
     *
     * @var array
     */
    private $options;

    /**
     * 
     * @param Router $router
     * @param ContainerInterface $container
     * @param TemplatingExtension $templating
     */
    public function __construct(Router $router, ContainerInterface $container, TwigEngine $templating)
    {
        $this->router = $router;
        $this->container = $container;
        $this->templating = $templating;
    }

    /**
     * 
     * @param array $options
     */
    public function setOptions($options = array())
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function pagination(QueryBuilder $query)
    {

        return array(
            'results' => $this->setQueryResult($query),
            'paginator' => $this->createPagination($query),
            'options' => $this->getOptions()
        );
    }

    private function setQueryResult(QueryBuilder $query)
    {

        $clone = clone $query;

        // get current page, if we have a parameters for page, or get 1 
        $current = $this->container->get('request')->attributes->get($this->options['page'], 1);

        // calculate offset
        $offset = $this->options['limit'] * $current - $this->options['limit'];

        // return QueryResult
        return $clone
                ->setMaxResults($this->options['limit'])
                ->setFirstResult($offset)->getQuery()->getResult();
    }

    private function createPagination(QueryBuilder $query)
    {

        // get current page, if we have a parameters for page, or get 1 
        $current = $this->container->get('request')->attributes->get($this->options['page'], 1);

        // clone QueryBuilder for count results
        $clone = clone $query;
        $count = $clone->select('count(' . $clone->getRootAliases()[0] . ')')->getQuery()->getSingleScalarResult();

        $items = ceil($count / $this->options['limit']);

        $start = $current - $this->options['offset'] > 1 ? $current - $this->options['offset'] : 1;
        $end = $current + $this->options['offset'] < $items ? $current + $this->options['offset'] : $items;

        $pages = array();
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $this->pageAttribute($i);
        }

        if ($start > 1) {
            $end_min = 1 + $this->options['offset'] >= $start ? $start - 1 : 1 + $this->options['offset'];
            if ($start - $end_min > 1) {
                array_unshift($pages, null);
            }
            for ($i = $end_min; $i >= 1; $i--) {
                array_unshift($pages, $this->pageAttribute($i));
            }
        }

        if ($items - $end >= 1 ) {
            if ($items - $this->options['offset'] > $end + 1 ) {
                $pages[] = null;
            }
            $end_max = $items - $this->options['offset'] <= $end ? $end + 1 : $items - $this->options['offset'];
            for ($i = $end_max; $i <= $items; $i++) {
                $pages[] = $this->pageAttribute($i);
            }
        }
        
        return array(
            'first' => $this->pageAttribute(1),
            'prev' => $current > 1 ? $this->pageAttribute($current - 1) : null,
            'pages' => $pages,
            'current' => $current,
            'next' => $current < $items ? $this->pageAttribute($current + 1) : null,
            'last' => $this->pageAttribute($items),
            'total' => $count,
            'total_page' => $items,
            'options' => $this->options
        );
    }

    private function pageAttribute($page)
    {
        return array(
            'page' => $page,
            'url' => $this->router->generate($this->container->get('request')->get('_route'), array_merge(
                $this->container->get('request')->query->all(), 
                $this->container->get('request')->get('_route_params'), 
                array($this->options['page'] => $page)
        )));
    }

}
