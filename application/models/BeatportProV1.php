<?php
require_once 'Crawler/Abstract.php';

/**
*Beatport (PRO) track crawler V1.0
*
*/
class BeatportProV1 extends Crawler_Abstract{

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
                 $beatportHTML = file_get_contents("https://pro.beatport.com/search?q=".$trackname);
                 //replace all new lines with whitespaces!
                 $beatportHTML = preg_replace('/\s+/', ' ', trim($beatportHTML));



                 //get all the json data between "tracks": [ and };
                 //$pattern = "/\"tracks\"\: \[(.*?)};/s"  # FOR STRINGS WITH NEW LINES AND BREAKS
                 //$pattern = "/\"tracks\"\: \[(.*?)} \] };/s"; //FOR STRINGS WITHOUT NEW LINES AND BREAKS
                 $pattern = "/window\.Playables \= (.*?);/s"; //FOR STRINGS WITHOUT NEW LINES AND BREAKS
                 preg_match($pattern, $beatportHTML, $hits);
                 $hits = $hits[1];

                 $allJsonData = json_decode($hits);

                 //echo "<pre>"; print_r($allJsonData); echo "</pre>";

                 //create crawl result objects
                 $allData = array();
                 $resultCounter = 0;
                 foreach($allJsonData->tracks AS $jsonDataObject){
                                 //get crawl result and fill!
                                 $crawlResult = $this->resultFactory->getCrawlResultObject();

                                 $crawlResult->setTitle(html_entity_decode($jsonDataObject->title, ENT_QUOTES));
                                 $crawlResult->setRelease(html_entity_decode($jsonDataObject->release->name, ENT_QUOTES));
                                 foreach($jsonDataObject->artists AS $artistData){
                                         $crawlResult->setArtist(html_entity_decode($artistData->name, ENT_QUOTES));
                                         $crawlResult->setProducer(html_entity_decode($artistData->name, ENT_QUOTES));
                                 }

                                 if(isset($jsonDataObject->remixers)){
                                         foreach($jsonDataObject->remixers AS $remixerData){
                                                 $crawlResult->setRemixer(html_entity_decode($remixerData->name, ENT_QUOTES));
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
                                 if(isset($jsonDataObject->date)){
                                         $releaseDate    = new DateTime(html_entity_decode($jsonDataObject->date->released, ENT_QUOTES));
                                         $crawlResult->setReleaseDate($releaseDate);
                                 }

                                 if(isset($jsonDataObject->mix)){
                                         $crawlResult->setMix(html_entity_decode($jsonDataObject->mix, ENT_QUOTES));
                                 }

                                 if(isset($jsonDataObject->bpm)){
                                         $crawlResult->setBpm(html_entity_decode($jsonDataObject->bpm, ENT_QUOTES));
                                 }
                                 if(isset($jsonDataObject->key)){
                                         $crawlResult->setKey(html_entity_decode($jsonDataObject->key, ENT_QUOTES));
                                 }

                                 if(isset($jsonDataObject->images)){
                                         if(isset($jsonDataObject->images->dynamic)){
                                                 //its an dynamic url. delete({hq}) and replace ({w}{h})the data
                                                 $coverUrl       = $jsonDataObject->images->dynamic->url;
                                                 $coverUrl       = str_replace("{hq}", "", $coverUrl);
                                                 $coverUrl       = str_replace("{w}", "300", $coverUrl);
                                                 $coverUrl       = str_replace("{h}", "300", $coverUrl);
                                                 $coverUrl       = $coverUrl;
                                                 $crawlResult->setCoverUrl($coverUrl);

                                                 //set image data
                                                 $coverData    = @file_get_contents($coverUrl);
                                                 //if cover data found (e. g. the image from beatport is not available or corrupted), set the standard image as image data
                                                 if(!$coverData){
                                                         $crawlResult->setCoverUrl(URL."/images/ErrorTagImage.jpg");
                                                         $coverData    = file_get_contents(URL."/images/ErrorTagImage.jpg");
                                                 }

                                                 $crawlResult->setCoverData($coverData);
                                         }elseif(isset($jsonDataObject->images->medium)){
                                                 $coverUrl       = $jsonDataObject->images->medium->url;
                                                 $crawlResult->setCoverUrl($coverUrl);

                                                 //set cover binary
                                                 $crawlResult->setCoverData(file_get_contents($coverUrl));
                                         }
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