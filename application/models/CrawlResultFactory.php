<?php
/**
*Crawl result factory
*/
class CrawlResultFactory{

         /**
         *Method returns a crawl result object
         *Possible arguments "standard"
         *
         *@access public
         *@static
         *@param string OPTIONAL: the name of wanted crawl result type
         *@returns CrawlResult_Interface The crawl result object
         *@throws CrawlResultFactory_Exception
         */
         public static function getCrawlResultObject($type = NULL)
         {
                 switch (strtolower($type)){
                         case 'standard':
                                 require_once 'CrawlResult.php';
                                 $cr    = new CrawlResult();
                                 break;
                         default:
                                 require_once 'CrawlResult.php';
                                 $cr    = new CrawlResult();
                                 break;
                 }
                 return $cr;
         }

}

?>