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

use ONGR\RepositoryCrawlerBundle;
use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\ElasticsearchBundle\Command\AbstractElasticsearchCommand;
use Symfony\Component\Console\Input\InputArgument;
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
            ->addArgument('context', InputArgument::REQUIRED, 'Crawler Context name')
            ->addOption('scroll-id', null, InputOption::VALUE_REQUIRED, 'Result scroll ID')
            ->addOption('async', null, InputOption::VALUE_NONE, 'Run crawling in asynchronous mode using celery');
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

        if ($input->getOption('async')) {
            $repositoryCrawler->runAsync($input->getArgument('context'), $input->getOption('scroll-id'));
        } else {
            $repositoryCrawler->run($input->getArgument('context'));
        }
        $output->writeln('');
        $output->writeln(sprintf('<info>Job finished in %.2f s</info>', microtime(true) - $start));
        $output->writeln(sprintf('<info>Memory usage: %.2f MB</info>', memory_get_peak_usage() >> 20));
    }
}
