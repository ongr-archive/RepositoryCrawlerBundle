<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Holds pipeline context for crawler.
 */
class CrawlerPipelineContext
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Result count.
     *
     * @var int
     */
    protected $resultCount = 0;

    /**
     * Progress helper.
     *
     * @var ProgressHelper
     */
    protected $progress;

    /**
     * Creates and returns instance of progress helper.
     *
     * @param int $count
     *
     * @return null|ProgressHelper
     */
    protected function getProgressHelper($count)
    {
        if (class_exists('\Symfony\Component\Console\Helper\ProgressBar')) {
            $progress = new ProgressBar($this->output, $count);
            $progress->start();
        } else {
            // This is for backwards compatibility only.
            // @codeCoverageIgnoreStart
            $progress = new ProgressHelper();
            $progress->start($this->output, $count);
            // @codeCoverageIgnoreEnd
        }

        return $progress;
    }

    /**
     * @param null|OutputInterface $output
     */
    public function __construct($output = null)
    {
        $this->output = $output;
    }

    /**
     * Adds results to totals counter.
     *
     * @param int $resultCount
     */
    public function addResults($resultCount)
    {
        $this->resultCount += $resultCount;
    }

    /**
     * Returns the results count of crawler pipeline context.
     *
     * @return int
     */
    public function getResultCount()
    {
        return $this->resultCount;
    }

    /**
     * Outputs progress to console, if output interface is present.
     */
    public function advanceProgress()
    {
        if ($this->output === null) {
            return;
        }

        if ($this->progress === null) {
            $this->progress = $this->getProgressHelper($this->getResultCount());
        }

        $this->progress->advance();
    }
}
