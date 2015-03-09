<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Command;

use ONGR\ElasticsearchBundle\Command\AbstractElasticsearchCommand;
use ONGR\ElasticsearchBundle\Command\AbstractManagerAwareCommand;
use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Repository crawler.
 */
class RepositoryCrawlerCommand extends AbstractManagerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('ongr:repository-crawler:crawl');

        $this
            ->setDescription('Repository crawler')
            ->addOption(
                'event-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set specific pipeline event name (see documentation for details)',
                null
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);

        /** @var Crawler $repositoryCrawler */
        $repositoryCrawler = $this->getContainer()->get('ongr_repository_crawler.crawler');
        $repositoryCrawler->setOutput($output);
        $eventName = $input->getOption('event-name');

        if ($eventName != null) {
            $repositoryCrawler->setTarget($eventName);
        }

        $repositoryCrawler->startCrawler('repository_crawler.');

        $output->writeln('');
        $output->writeln(sprintf('<info>Job finished in %.2f s</info>', microtime(true) - $start));
        $output->writeln(sprintf('<info>Memory usage: %.2f MB</info>', memory_get_peak_usage() >> 20));
    }
}
