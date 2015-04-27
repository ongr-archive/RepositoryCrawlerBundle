=================
Crawl repository.
=================

- Command name: `ongr:repository-crawler:crawl`.
- Description: Crawls repositories using pipeline events.
- Options:

    `-event-name` : Sets specific pipeline event name.

---------
Examples:
---------

`ongr:repository-crawler:crawl`: will crawl repositories using `ongr.pipeline.repository_crawler.default.[...]`
pipeline event name pattern.

`ongr:repository-crawler:crawl -event-name myEventName`: will crawl repositories using
`ongr.pipeline.repository_crawler.myEventName.[...]` pipeline event name pattern, like in this config.yml example:

.. code-block:: yml

    # Source event myEventName.
    ongr.pipeline.repository_crawler.repository_source:
       class: %ongr_repository_crawler.crawler.source.class%
       arguments:
           - @es.manager
           - AcmeTestBundle:Product
       tags:
           - { name: kernel.event_listener, event: ongr.pipeline.repository_crawler.myEventName.source, method: onSource }

    # Modify event myEventName.
    ongr.pipeline.repository_crawler.crawler_process_document:
        class: %ongr_repository_crawler.crawler.modifier.class%
        arguments:
            - @service_container
        tags:
            - { name: kernel.event_listener, event: ongr.pipeline.repository_crawler.myEventName.modify, method: onModify }

..

`ongr.pipeline.repository_crawler.repository_source` service will define data source - `@es.manager`, `AcmeTestBundle:Product`

`ongr.pipeline.repository_crawler.crawler_process_document` service will define event (action) on each iteration.


