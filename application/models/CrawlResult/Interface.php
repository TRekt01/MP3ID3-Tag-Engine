<?php
/**
*Interface for MP3-ID3-Crawl-Results
*/
interface CrawlResult_Interface{
         const TITLE = "title";
         const RELEASE = "release";
         const ARTIST = "artist";
         const REMIXER = "remixer";
         const PRODUCER = "producer";
         const GENRE = "genre";
         const LABEL = "label";
         const RELEASEDATE = "releasedate";
         const MIX = "mix";
         const BPM = "bpm";
         const KEY = "key";
         const COMMENT = "comment";
         const COVERURL = "coverurl";
         const COVERDATA = "coverdata";

         public function getTitle();
         public function getRelease();
         public function getArtist();
         public function getRemixer();
         public function getProducer();
         public function getGenre();
         public function getLabel();
         public function getReleaseDate();
         public function getMix();
         public function getBpm();
         public function getKey();
         public function getComment();
         public function getCoverUrl();
         public function getCoverData();

         public function setTitle($title);
         public function setRelease($release);
         public function setArtist($artist);
         public function setRemixer($remixer);
         public function setProducer($producer);
         public function setGenre($genre);
         public function setLabel($label);
         public function setReleaseDate(DateTime $date);
         public function setMix($mix);
         public function setBpm($bpm);
         public function setKey($key);
         public function setComment($comment);
         public function setCoverUrl($coverUrl);
         public function setCoverData($coverData);

         public function getCompleteDataAsArray($withPictureBinary = FALSE);

}

?>