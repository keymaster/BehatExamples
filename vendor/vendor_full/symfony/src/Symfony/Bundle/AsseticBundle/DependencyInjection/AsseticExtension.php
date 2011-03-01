<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\AsseticBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Semantic asset configuration.
 *
 * @author Kris Wallsmith <kris.wallsmith@symfony-project.com>
 */
class AsseticExtension extends Extension
{
    /**
     * Loads the configuration.
     *
     * When the debug flag is true, files in an asset collections will be
     * rendered individually.
     *
     * In XML:
     *
     *     <assetic:config
     *         debug="true"
     *         use-controller="true"
     *         read-from="/path/to/web"
     *         write-to="s3://mybucket"
     *         closure="/path/to/google_closure/compiler.jar"
     *         yui="/path/to/yuicompressor.jar"
     *         default-javascripts-output="js/build/*.js"
     *         default-stylesheets-output="css/build/*.css"
     *     />
     *
     * In YAML:
     *
     *     assetic:
     *         debug: true
     *         use_controller: true
     *         read_from: /path/to/web
     *         write_to: s3://mybucket
     *         closure: /path/to/google_closure/compiler.jar
     *         yui: /path/to/yuicompressor.jar
     *         default_javascripts_output: js/build/*.js
     *         default_stylesheets_output: css/build/*.css
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('assetic.xml');
        $loader->load('templating_twig.xml');
        // $loader->load('templating_php.xml'); // not ready yet

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->process($configuration->getConfigTree($container->getParameter('kernel.debug')), $configs);

        $container->setParameter('assetic.debug', $config['debug']);
        $container->setParameter('assetic.use_controller', $config['use_controller']);
        $container->setParameter('assetic.read_from', $config['read_from']);
        $container->setParameter('assetic.write_to', $config['write_to']);
        $container->setParameter('assetic.default_javascripts_output', $config['default_javascripts_output']);
        $container->setParameter('assetic.default_stylesheets_output', $config['default_stylesheets_output']);

        if (isset($config['closure'])) {
            $container->setParameter('assetic.google_closure_compiler_jar', $config['closure']);
            $loader->load('google_closure_compiler.xml');
        }

        if (isset($config['yui'])) {
            $container->setParameter('assetic.yui_jar', $config['yui']);
            $loader->load('yui_compressor.xml');
        }

        if ($container->getParameterBag()->resolveValue($container->getParameterBag()->get('assetic.use_controller'))) {
            $loader->load('controller.xml');
            $container->setParameter('assetic.twig_extension.class', '%assetic.twig_extension.dynamic.class%');
        } else {
            $loader->load('asset_writer.xml');
            $container->setParameter('assetic.twig_extension.class', '%assetic.twig_extension.static.class%');
        }

        if ($container->hasParameter('assetic.less.compress')) {
            $container->getDefinition('assetic.filter.less')->addMethodCall('setCompress', array('%assetic.less.compress%'));
        }

        $this->registerFormulaResources($container);
    }

    protected function registerFormulaResources(ContainerBuilder $container)
    {
        // bundle views/ directories
        $am = $container->getDefinition('assetic.asset_manager');
        foreach ($container->getParameter('kernel.bundles') as $name => $class) {
            $rc = new \ReflectionClass($class);
            if (is_dir($dir = dirname($rc->getFileName()).'/Resources/views')) {
                foreach (array('twig', 'php') as $engine) {
                    $container->setDefinition(
                        'assetic.'.$engine.'_directory_resource.'.$name,
                        $this->createDirectoryResourceDefinition($name, $dir, $engine)
                    );
                }
            }
        }

        // kernel views/ directory
        if (is_dir($dir = $container->getParameter('kernel.root_dir').'/views')) {
            foreach (array('twig', 'php') as $engine) {
                $container->setDefinition(
                    'assetic.'.$engine.'_directory_resource.kernel',
                    $this->createDirectoryResourceDefinition('', $dir, $engine)
                );
            }
        }
    }

    /**
     * @todo decorate an abstract xml definition
     */
    protected function createDirectoryResourceDefinition($bundle, $dir, $engine)
    {
        $definition = new Definition('%assetic.directory_resource.class%');

        $definition
            ->addArgument(new Reference('templating.loader'))
            ->addArgument($bundle)
            ->addArgument($dir)
            ->addArgument('/^[^.]+\.[^.]+\.'.$engine.'$/')
            ->addTag('assetic.templating.'.$engine)
            ->addTag('assetic.formula_resource', array('loader' => $engine))
            ->setPublic(false)
        ;

        return $definition;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/assetic';
    }
}
