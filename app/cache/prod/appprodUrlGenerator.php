<?php

/**
 * appprodUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appprodUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    static protected $declaredRouteNames = array(
       'homepage' => true,
       'hello' => true,
       'user_profile' => true,
    );

    /**
     * Constructor.
     */
    public function __construct(array $context = array(), array $defaults = array())
    {
        $this->context = $context;
        $this->defaults = $defaults;
    }

    public function generate($name, array $parameters, $absolute = false)
    {
        if (!isset(self::$declaredRouteNames[$name])) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not exist.', $name));
        }

        $escapedName = str_replace('.', '__', $name);

        list($variables, $defaults, $requirements, $tokens) = $this->{'get'.$escapedName.'RouteInfo'}();

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }

    protected function gethomepageRouteInfo()
    {
        return array(array (), array_merge($this->defaults, array (  '_controller' => 'Sensio\\HelloBundle\\Controller\\HelloController::welcomeAction',)), array (), array (  0 =>   array (    0 => 'text',    1 => '/',    2 => '',    3 => NULL,  ),));
    }

    protected function gethelloRouteInfo()
    {
        return array(array (  'name' => '{name}',), array_merge($this->defaults, array (  '_controller' => 'Sensio\\HelloBundle\\Controller\\HelloController::indexAction',)), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '{name}',    3 => 'name',  ),  1 =>   array (    0 => 'text',    1 => '/',    2 => 'hello',    3 => NULL,  ),));
    }

    protected function getuser_profileRouteInfo()
    {
        return array(array (  'slug' => '{slug}',), array_merge($this->defaults, array (  '_controller' => 'Sensio\\HelloBundle\\Controller\\UserController::profileEditFormAction',)), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '{slug}',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/',    2 => 'profile',    3 => NULL,  ),  2 =>   array (    0 => 'text',    1 => '/',    2 => 'user',    3 => NULL,  ),));
    }
}
