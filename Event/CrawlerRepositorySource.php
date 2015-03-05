<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
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
     * @var Repository Elasticsearch repository.
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
        $sourceEvent->addSource($this->getAllDocuments());
    }

    /**
     * Gets all documents by given type.
     *
     * @return DocumentIterator
     */
    public function getAllDocuments()
    {
        $matchAllQuery = new MatchAllQuery();
        $search = $this->repository->createSearch()->addQuery($matchAllQuery);

        return $this->repository->execute($search);
    }
}
