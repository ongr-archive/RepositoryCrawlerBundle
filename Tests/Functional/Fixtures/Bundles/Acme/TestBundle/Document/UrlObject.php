<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Functional\Fixtures\Bundles\Acme\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * Default UrlObject document.
 *
 * @ES\Object
 */
class UrlObject
{
    /**
     * @var string
     *
     * @ES\Property(name="url", type="string")
     */
    public $url;

    /**
     * @var string
     *
     * @ES\Property(name="key", type="string", index="no")
     */
    public $urlKey;

    /**
     * @var CdnObject
     *
     * @ES\Property(name="cdn", type="object", objectName="AcmeTestBundle:CdnObject")
     */
    public $cdn;

    /**
     * @param string $urlKey
     */
    public function setUrlKey($urlKey)
    {
        $this->urlKey = $urlKey;
    }

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return $this->urlKey;
    }
}
