<?php
require_once 'Crawler/Abstract.php';

/**
*Beatport track crawler V1.0
*
*/
class Beatportv1 extends Crawler_Abstract{

         /**
         *Method searchs beatport for ID3 Data
         *
         *@access public
         *@param String The trackname (search value) Artist - Trackname (Mix Version)
         *@return array Containing CrawlResult Objects!
         */
         public function searchId3Tags($trackname){
                 //prepare search value (delete &)
                 $trackname = str_replace("&", "", $trackname);

                 //prepare search val (replace +)
                 $trackname = str_replace("+", "%2B", $trackname);

                 //Build string for beatport http search url
                 $trackname = str_replace(" ", "+", $trackname);

                 //get complete HTML
                 $beatportHTML = file_get_contents("http://www.beatport.com/search?query=".$trackname);

                 //get all the data-json="..." infos
                 $pattern = "'data-json=\'(.*?)\'>'";
                 preg_match_all($pattern, $beatportHTML, $hits);
                 $hits = $hits[1];

                 //create crawl result objects
                 $allData = array();
                 $resultCounter = 0;
                 foreach($hits AS $jsonData){
                                 $jsonDataObject = json_decode($jsonData);

                                 //get crawl result and fill!
                                 $crawlResult = $this->resultFactory->getCrawlResultObject();

                                 $crawlResult->setTitle(html_entity_decode($jsonDataObject->title, ENT_QUOTES));
                                 $crawlResult->setRelease(html_entity_decode($jsonDataObject->release->name, ENT_QUOTES));
                                 foreach($jsonDataObject->artists AS $artistData){
                                         if($artistData->type == "artist"){
                                                 $crawlResult->setArtist(html_entity_decode($artistData->name, ENT_QUOTES));
                                                 $crawlResult->setProducer(html_entity_decode($artistData->name, ENT_QUOTES));
                                         }

                                         if($artistData->type == "remixer"){
                                                 $crawlResult->setRemixer(html_entity_decode($artistData->name, ENT_QUOTES));
                                                 $crawlResult->setProducer(html_entity_decode($artistData->name, ENT_QUOTES));
                                         }
                                 }

                                 if(isset($jsonDataObject->genres)){
                                         $genreId3 = "";
                                         foreach($jsonDataObject->genres AS $genre){
                                                 $genreId3 .= $genre->name." ";
                                         }
                                         $crawlResult->setGenre(html_entity_decode($genreId3, ENT_QUOTES));
                                 }
                                 if(isset($jsonDataObject->label)){
                                         $crawlResult->setLabel(html_entity_decode($jsonDataObject->label->name, ENT_QUOTES));
                                 }

                                 //transform release ddate into datetime object for commiting to crawl result
                                 if(isset($jsonDataObject->releaseDate)){
                                         $releaseDate    = new DateTime(html_entity_decode($jsonDataObject->releaseDate, ENT_QUOTES));
                                         $crawlResult->setReleaseDate($releaseDate);
                                 }
                                 if(isset($jsonDataObject->mixName)){
                                         $crawlResult->setMix(html_entity_decode($jsonDataObject->mixName, ENT_QUOTES));
                                 }
                                 if(isset($jsonDataObject->bpm)){
                                         $crawlResult->setBpm(html_entity_decode($jsonDataObject->bpm, ENT_QUOTES));
                                 }
                                 if(isset($jsonDataObject->key)){
                                         $crawlResult->setKey(html_entity_decode($jsonDataObject->key->shortName, ENT_QUOTES));
                                 }

                                 if(isset($jsonDataObject->dynamicImages)){
                                         //its an dynamic url. delete({hq}) and replace ({w}{h})the data
                                         $coverUrl       = $jsonDataObject->dynamicImages->main->url;
                                         $coverUrl       = str_replace("{hq}", "", $coverUrl);
                                         $coverUrl       = str_replace("{w}", "300", $coverUrl);
                                         $coverUrl       = str_replace("{h}", "300", $coverUrl);
                                         $coverUrl       = "http:".$coverUrl;
                                         $crawlResult->setCoverUrl($coverUrl);

                                         //set cover binary
                                         $crawlResult->setCoverData(file_get_contents($coverUrl));
                                 }

                                 $allData[] = $crawlResult;

                                 $resultCounter++;
                                 if($resultCounter >= 10){
                                         break;
                                 }
                 }
                 unset($hits);
                 return $allData;
         }
}

?>