<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event which informs about next scroll ID availability.
 */
class CrawlerChunkEvent extends Event
{
    /**
     * @var string
     */
    private $scrollId;

    /**
     * Constructor.
     *
     * @param string $scrollId
     */
    public function __construct($scrollId)
    {
        $this->scrollId = $scrollId;
    }

    /**
     * Gets scrollId.
     *
     * @return int
     * @codeCoverageIgnore
     */
    public function getScrollId()
    {
        return $this->scrollId;
    }
}
