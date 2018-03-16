<?php
require_once 'Interface.php';

/**
*Abstract crawler class
*/
abstract class Crawler_Abstract implements Crawler_Interface{
         protected $resultFactory;

         /**
         *Constructor
         *
         *@param CrawlResultFactory ResultFactory, needed to create crawl results
         */
         public function __construct(CrawlResultFactory $crf){
                 $this->resultFactory = $crf;
         }


}

?>