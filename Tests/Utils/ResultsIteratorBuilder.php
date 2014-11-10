<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Utils;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchBundle\Result\DocumentScanIterator;

/**
 * Helper class to build results iterator mock.
 */
class ResultsIteratorBuilder
{
    /**
     * @param \PHPUnit_Framework_TestCase $test
     * @param array $documents
     * @param bool $chunk
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|DocumentIterator|DocumentScanIterator
     */
    public static function getMock($test, array $documents, $chunk = false)
    {
        if ($chunk) {
            $class = 'ONGR\ElasticsearchBundle\Result\DocumentScanIterator';
        } else {
            $class = 'ONGR\ElasticsearchBundle\Result\DocumentIterator';
        }

        $array = new \ArrayIterator($documents);

        $iterator = $test->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $iterator->expects($test->any())->method('current')->will(
            $test->returnCallback(
                function () use ($array) {
                    return $array->current();
                }
            )
        );

        $iterator->expects($test->any())->method('next')->will(
            $test->returnCallback(
                function () use ($array) {
                    $array->next();
                }
            )
        );

        $iterator->expects($test->any())->method('key')->will(
            $test->returnCallback(
                function () use ($array) {
                    return $array->key();
                }
            )
        );

        $iterator->expects($test->any())->method('valid')->will(
            $test->returnCallback(
                function () use ($array) {
                    return $array->valid();
                }
            )
        );

        $iterator->expects($test->any())->method('rewind')->will(
            $test->returnCallback(
                function () use ($array) {
                    $array->rewind();
                }
            )
        );

        $iterator->expects($test->any())->method('count')->will(
            $test->returnCallback(
                function () use ($array) {
                    $array->count();
                }
            )
        );

        return $iterator;
    }
}
