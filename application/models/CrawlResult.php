<?php
require_once "CrawlResult/Interface.php";

/**
*ID3Tag-Crawl-Result class
*/
class CrawlResult implements CrawlResult_Interface{

         protected $title;
         protected $release;
         protected $artist;
         protected $remixer = "-";
         protected $producer;
         protected $genre;
         protected $label;
         protected $releaseDate;
         protected $mix;
         protected $bpm = "-";
         protected $key = "-";
         protected $comment;

         protected $coverUrl;
         protected $coverData;

         /**
         *Getter for title
         *
         *@return string Trackname
         */
         public function getTitle(){
                 return $this->title;
         }

         /**
         *Setter for title
         */
         public function setTitle($title){
                 $this->title = $title;
         }

         /**
         *Getter for release
         *
         *@return string Album
         */
         public function getRelease(){
                 return $this->release;
         }

         /**
         *Setter for release
         */
         public function setRelease($release){
                 $this->release = $release;
         }

         /**
         *Getter for artist
         *
         *@return string Artist
         */
         public function getArtist(){
                 return $this->artist;
         }

         /**
         *Setter for artist
         */
         public function setArtist($artist){
                 if(empty($this->artist)){
                         $this->artist = $artist;
                 }else{
                         $this->artist .= ", ".$artist;
                 }
         }

         /**
         *Getter for remixer
         *
         *@return string Remixer
         */
         public function getRemixer(){
                 return $this->remixer;
         }

         /**
         *Setter for remixer
         */
         public function setRemixer($remixer){
                 if(empty($this->remixer) || $this->remixer == "-"){
                         $this->remixer = $remixer;
                 }else{
                         $this->remixer .= ", ".$remixer;
                 }
         }

         /**
         *Getter for producer
         *
         *@return string Producer
         */
         public function getProducer(){
                 return $this->producer;
         }

         /**
         *Setter for producer
         */
         public function setProducer($producer){
                 $this->producer = $producer;
         }

         /**
         *Getter for genre
         *
         *@return string Genre name
         */
         public function getGenre(){
                 return $this->genre;
         }

         /**
         *Setter for genre
         */
         public function setGenre($genre){
                 $this->genre = $genre;
         }

         /**
         *Getter for label
         *
         *@return string Label
         */
         public function getLabel(){
                 return $this->label;
         }

         /**
         *Setter for label
         */
         public function setLabel($label){
                 $this->label = $label;
         }

         /**
         *Getter for releaseDate
         *
         *@return DateTime A DateTime Object
         */
         public function getReleaseDate(){
                 return $this->releaseDate;
         }

         /**
         *Setter for ReleaseDate
         *
         *@param DateTime a DatetimeObject containing the realase date
         */
         public function setReleaseDate(DateTime $releaseDate){
                 $this->releaseDate = $releaseDate;
         }

         /**
         *Getter for mix
         *
         *@return string Mixversion
         */
         public function getMix(){
                 return $this->mix;
         }

         /**
         *Setter for mix
         */
         public function setMix($mix){
                 $this->mix = $mix;
         }

         /**
         *Getter for bpm
         *
         *@return string Bpm
         */
         public function getBpm(){
                 return $this->bpm;
         }

         /**
         *Setter for bpm
         */
         public function setBpm($bpm){
                 $this->bpm = $bpm;
         }

         /**
         *Getter for key
         *
         *@return string Initial key
         */
         public function getKey(){
                 return $this->key;
         }

         /**
         *Setter for key
         */
         public function setKey($key){
                 $this->key = $key;
         }

         /**
         *Getter for comment
         *
         *@return string Comment
         */
         public function getComment(){
                 return $this->comment;
         }

         /**
         *Setter for comment
         */
         public function setComment($comment){
                 $this->comment  = $comment;
         }

         /**
         *Getter for cover
         *
         *@return string CoverUrl
         */
         public function getCoverUrl(){
                 return $this->coverUrl;
         }

         /**
         *Setter for cover
         */
         public function setCoverUrl($coverUrl){
                 $this->coverUrl  = $coverUrl;
         }

         /**
         *Getter for cover
         *
         *@return string CoverData!
         */
         public function getCoverData(){
                 return $this->coverData;
         }

         /**
         *Setter for cover
         */
         public function setCoverData($coverData){
                 $this->coverData  = $coverData;
         }

         /**
         *Returns all data as array
         *
         *@access public
         *@param boolean Commit TRUE if you also want that the binary data of the album picture is included in the array
         *@return array complete data as array
         */
         public function getCompleteDataAsArray($withPictureBinary = FALSE){
                 return $data = array(   self::TITLE => $this->title,
                                         self::RELEASE => $this->release,
                                         self::ARTIST => $this->artist,
                                         self::REMIXER => $this->remixer,
                                         self::PRODUCER => $this->producer,
                                         self::GENRE => $this->genre,
                                         self::LABEL => $this->label,
                                         self::RELEASEDATE => $this->releaseDate->format('Y-m-d'),
                                         self::MIX => $this->mix,
                                         self::BPM => $this->bpm,
                                         self::KEY => $this->key,
                                         self::COMMENT => $this->comment,
                                         self::COVERURL => $this->coverUrl
                                         );
         }
}



?>