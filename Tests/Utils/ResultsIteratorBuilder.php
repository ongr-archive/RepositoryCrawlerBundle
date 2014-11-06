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

namespace ONGR\RepositoryCrawlerBundle\Tests\Utils;

use ONGR\ElasticsearchBundle\Result\AbstractResultsIterator;
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
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractResultsIterator|DocumentScanIterator
     */
    public static function getMock($test, array $documents, $chunk = false)
    {
        if ($chunk) {
            $class = 'ONGR\ElasticsearchBundle\Result\AbstractResultsIterator';
        } else {
            $class = 'ONGR\ElasticsearchBundle\Result\DocumentScanIterator';
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
