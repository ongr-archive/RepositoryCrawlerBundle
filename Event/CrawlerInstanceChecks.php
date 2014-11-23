<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

/**
 * Checks for required instances.
 */
class CrawlerInstanceChecks
{
    /**
     * Checks instance of pipeline context.
     *
     * @param mixed $pipelineContext
     *
     * @throws \LogicException
     */
    public static function checkPipelineContext($pipelineContext)
    {
        if (!($pipelineContext instanceof CrawlerPipelineContext)) {
            throw new \LogicException(
                'Crawler event listeners only accept events with CrawlerPipelineContext context.'
            );
        }
    }
}
