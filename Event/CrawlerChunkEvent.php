<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
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
    protected $scrollId;

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
     * Returns scroll ID.
     *
     * @return string
     */
    public function getScrollId()
    {
        return $this->scrollId;
    }
}
