<?php

use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherTrait;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcApp_KernelDevDebugContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    use PhpMatcherTrait;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
        $this->staticRoutes = array(
            '/order/create' => array(array(array('_route' => 'order_create', '_controller' => 'App\\Controller\\OrderController::create'), null, null, null, false, null)),
            '/product/create' => array(array(array('_route' => 'product_create', '_controller' => 'App\\Controller\\ProductController::create'), null, null, null, false, null)),
            '/product/create_cat' => array(array(array('_route' => 'product_create_cat', '_controller' => 'App\\Controller\\ProductController::create_cat'), null, null, null, false, null)),
            '/user/create' => array(array(array('_route' => 'user_create', '_controller' => 'App\\Controller\\UserController::create'), null, null, null, false, null)),
            '/user/check' => array(array(array('_route' => 'user_check', '_controller' => 'App\\Controller\\UserController::check_password'), null, null, null, false, null)),
            '/user' => array(array(array('_route' => 'user', '_controller' => 'App\\Controller\\UserController::index'), null, null, null, false, null)),
            '/product' => array(array(array('_route' => 'product', '_controller' => 'App\\Controller\\ProductController::index'), null, null, null, false, null)),
            '/order' => array(array(array('_route' => 'order', '_controller' => 'App\\Controller\\OrderController::index'), null, null, null, false, null)),
        );
        $this->regexpList = array(
            0 => '{^(?'
                    .'|/order/(?'
                        .'|read/([^/]++)(*:30)'
                        .'|update/([^/]++)(*:52)'
                        .'|delete/([^/]++)(*:74)'
                    .')'
                    .'|/product/(?'
                        .'|read(?'
                            .'|/([^/]++)(*:110)'
                            .'|_cat/([^/]++)(*:131)'
                        .')'
                        .'|update/([^/]++)(*:155)'
                        .'|delete/([^/]++)(*:178)'
                    .')'
                    .'|/user/(?'
                        .'|read/([^/]++)(*:209)'
                        .'|delete/([^/]++)(*:232)'
                        .'|ship/([^/]++)(*:253)'
                        .'|update/([^/]++)(*:276)'
                    .')'
                    .'|/_error/(\\d+)(?:\\.([^/]++))?(*:313)'
                .')(?:/?)$}sDu',
        );
        $this->dynamicRoutes = array(
            30 => array(array(array('_route' => 'order_read', '_controller' => 'App\\Controller\\OrderController::read'), array('id'), null, null, false, null)),
            52 => array(array(array('_route' => 'app_order_update', '_controller' => 'App\\Controller\\OrderController::update'), array('id'), null, null, false, null)),
            74 => array(array(array('_route' => 'order_delete', '_controller' => 'App\\Controller\\OrderController::delete'), array('id'), null, null, false, null)),
            110 => array(array(array('_route' => 'product_read', '_controller' => 'App\\Controller\\ProductController::read'), array('id'), null, null, false, null)),
            131 => array(array(array('_route' => 'product_read_cat', '_controller' => 'App\\Controller\\ProductController::read_cat'), array('id'), null, null, false, null)),
            155 => array(array(array('_route' => 'product_update', '_controller' => 'App\\Controller\\ProductController::update'), array('id'), null, null, false, null)),
            178 => array(array(array('_route' => 'product_delete', '_controller' => 'App\\Controller\\ProductController::delete'), array('id'), null, null, false, null)),
            209 => array(array(array('_route' => 'user_read', '_controller' => 'App\\Controller\\UserController::read'), array('id'), null, null, false, null)),
            232 => array(array(array('_route' => 'user_delete', '_controller' => 'App\\Controller\\UserController::delete'), array('id'), null, null, false, null)),
            253 => array(array(array('_route' => 'user_ship', '_controller' => 'App\\Controller\\UserController::ship'), array('id'), null, null, false, null)),
            276 => array(array(array('_route' => 'user_update', '_controller' => 'App\\Controller\\UserController::update'), array('id'), null, null, false, null)),
            313 => array(array(array('_route' => '_twig_error_test', '_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'), array('code', '_format'), null, null, false, null)),
        );
    }
}
