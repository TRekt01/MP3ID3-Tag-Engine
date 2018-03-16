<?php
/**
*Interface for MP3-ID3-Tag-Crawl-Engine
*/
interface Crawler_Interface{

         public function __construct(CrawlResultFactory $resultFactory);
         public function searchId3Tags($trackname);

}

?>