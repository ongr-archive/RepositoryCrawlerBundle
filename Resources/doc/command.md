## Crawl repository.

* Command name: `ongr:repository-crawler:crawl`.
* Description: Crawls repositories using pipeline events.
* Options:

    `-event-name` : Sets specific pipeline event name. 

* Examples: 
    
    `ongr:repository-crawler:crawl`: will crawl repositories using `ongr.pipeline.repository_crawler.default.[...]` pipeline event name pattern.
    
    `ongr:repository-crawler:crawl -event-name myEventName`: will crawl repositories using `ongr.pipeline.repository_crawler.myEventName.[...]` pipeline event name pattern.
    
