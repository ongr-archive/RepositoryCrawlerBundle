<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ConnectionsBundle\Pipeline\Event\SourcePipelineEvent;

/**
 * Provides data from Elasticsearch repository.
 */
class CrawlerRepositorySource extends AbstractCrawlerSource
{
    /**
     * Elasticsearch repository.
     *
     * @var Repository $repository
     */
    protected $repository;

    /**
     * Provides default search for Elasticsearch query.
     *
     * @return Search
     */
    protected function getSearch()
    {
        $search = new Search();
        return $search;
    }

    /**
     * Constructor.
     *
     * @param Manager $manager
     * @param string  $repositoryName
     */
    public function __construct(Manager $manager, $repositoryName)
    {
        $this->repository = $manager->getRepository($repositoryName);
    }

    /**
     * Source provider event.
     *
     * @param SourcePipelineEvent $sourceEvent
     */
    public function onSource(SourcePipelineEvent $sourceEvent)
    {
        $results = $this->repository->execute($this->getSearch(), Repository::RESULTS_OBJECT);
        $this->registerSource($sourceEvent, $results);
    }
}
