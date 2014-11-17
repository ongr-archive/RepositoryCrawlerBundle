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

use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\ElasticsearchBundle\Command\AbstractElasticsearchCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Repository crawler.
 */
class RepositoryCrawlerCommand extends AbstractElasticsearchCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ongr:repository-crawler:crawl')
            ->setDescription('Repository crawler')
            ->addOption(
                'use-event-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'use ongr.pipeline.repository_crawler.{event-name}.* instead of
                ongr.pipeline.repository_crawler.default.*',
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
        $repositoryCrawler = $this->getContainer()->get('ongr.repository_crawler.crawler');
        $repositoryCrawler->setOutput($output);
        $eventName = $input->getOption('use-event-name');

        if ($eventName != null) {
            $repositoryCrawler->setEventNameInterfix($eventName);
        }

        $repositoryCrawler->run($this->getContainer());
        $output->writeln('');
        $output->writeln(sprintf('<info>Job finished in %.2f s</info>', microtime(true) - $start));
        $output->writeln(sprintf('<info>Memory usage: %.2f MB</info>', memory_get_peak_usage() >> 20));
    }
}
