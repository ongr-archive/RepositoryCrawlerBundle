<?php

namespace ONGR\RepositoryCrawlerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use ONGR\RepositoryCrawlerBundle\DependencyInjection\Compiler\CrawlerListenerPass;

/**
 * Repository crawler bundle.
 */
class ONGRRepositoryCrawlerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CrawlerListenerPass());
    }
}
