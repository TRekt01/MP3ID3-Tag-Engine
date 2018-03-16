<?php
require_once "FileHelper/Exception.php";
require_once "FileHelper/Interface.php";
/**
*FileHelper-Class provides methods for reading, storing, editing etc. mp3s from a specified directory
*/
class FileHelper implements FileHelper_Interface{

         protected $dir;
         protected $getID3 = NULL;

         protected $allFiles = Array();
         protected $mp3Files = Array();

         protected $numberOfFoundFiles = 0;
         protected $numberOfFoundMp3Files = 0;


         /**
         *Constructor
         *
         *@param string the Directory
         *@param Zend_Validate_File_MimeType Zends MimeType validator
         */
         public function __construct($dir, getID3 $getID3){
                 if(!is_dir($dir)) {
                         throw new FileHelper_Exception("Das angegebene Mp3-Directory existiert nicht! Bitte geben Sie unter \"Einstellungen\" das Verzeichnis an, welches die zu bearbeitenden MP3-Dateien enthält.", 1);
                 }
                 $this->getID3   = $getID3;
                 $this->dir = $dir;
         }

         /**
         *Method returns all Files from the dir
         *
         *@param boolean OPTIONAL: TRUE for a rescan of the dir. Use false if no rescan is needed because of an earlier dir-scan standard = FALSE
         *@return array All files from the dir
         */
         public function rescanFilesInDir(){
                 $allFiles = scandir($this->dir);

                 $helper = array();

                 foreach($allFiles AS $fileentry){
                         $helper[]       = realpath($this->dir."/".$fileentry);
                 }

                 $this->allFiles = $helper;
                 return $helper;
         }

         /**
         *Method reads all mp3-files from a specific dir
         *
         *@access public
         *@static
         *@param boolean OPTIONAL Commit TRUE if you want the filenames with complete path, otherwise you will get filenames without path! sandard = FALSE
         *@return array Returns an Array with all the mp3files the dir is containing
         */
         public function getMp3FilesFromDir($withDirInfo = FALSE){
                 $this->rescanFilesInDir();
                 $files = $this->allFiles;

                 //Alles was kein File ist muss raus!
                 foreach($files As $key => $file){
                         if(!is_file($file)){
                                 unset($files[$key]);
                         }
                 }

                 $this->numberOfFoundFiles       = count($files);

                 //Alles ohne mp3-Dateiendung muss raus!
                 foreach($files AS $key => $file){
                         $pi     = pathinfo($file);

                         //Keine Dateiendung oder Dateiendung != mp3
                         if(!isset($pi['extension']) || strtolower($pi['extension']) != "mp3"){
                                 unset($files[$key]);
                         }
                         unset($pi);
                 }

                 $getID3 = $this->getID3Object();
                 foreach($files AS $key => $file){
                         if($fp = fopen($file, 'rb')) {
                                 $getID3->openfile($file);
                                 if(empty($getID3->info['error'])){
                                         getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.id3v2.php', __FILE__, true);
                                         $getid3_id3v2 = new getid3_id3v2($getID3);
                                         $getid3_id3v2->Analyze();

                                         fseek($fp, $getID3->info['avdataoffset'], SEEK_SET);
                                         $formattest = fread($fp, 16);

                                         $fileInfo = $getID3->GetFileFormat($formattest);

                                         if($fileInfo['mime_type'] != "audio/mpeg" || $fileInfo['module'] != "mp3"){
                                                 unset($files[$key]);
                                         }
                                         fclose($fp);
                                 }else{
                                         echo 'Failed to getID3->openfile "'.htmlentities($filename).'"<br>';
                                 }

                         }else{
                                 throw new FileHelper_Exception("Die angegebene Datei ".htmlentities($filename)." konnte nicht geöffnet werden! ", 3);
                         }
                 }

                 $this->numberOfFoundMp3Files    = count($files);

                 //store mit pfad
                 $this->mp3Files = $files;
                 if(!$withDirInfo){
                         foreach($files AS $key => $filepath){
                                 $files[$key] = basename($filepath);
                         }
                         //oder ohne
                         $this->mp3Files = $files;
                 }

                 return $files;
         }

         /**
         *Method renames a file
         *
         *@access public
         *@static
         *@param string the directory where the file is located in
         *@param string the old filename
         *@param string the new filename
         *@throws FileHelper_Exception
         */
         public static function renameFile($directory, $oldFileName, $newFileName){
                 if(is_dir($directory)){
                         $oldFile        = $directory."/".$oldFileName;
                         $newFile        = $directory."/".$newFileName;
                         if(is_File($oldFile)){
                                 if(@rename($oldFile, $newFile)===FALSE){
                                         throw new FileHelper_Exception("Das Umbenennen der Datei ".htmlentities($oldFileName)." schlug fehl! Vorgang abgebrochen!", 4);
                                 }
                         }else{
                                 throw new FileHelper_Exception("Die angegebene Datei ".htmlentities($oldFile)." existiert nicht! Rename abgebrochen! ", 5);
                         }
                 }else{
                         throw new FileHelper_Exception("Das angegebene Verzeichnis ".htmlentities($directory)." existiert nicht! Rename abgebrochen! ", 6);
                 }
         }

         /**
         *Method writes ID3-Tags
         *
         *@access public
         *@param string the path to the file
         *@param CrawlResult_Interface The crawl result containing the id3 data
         */
         public function writeId3Data($file, CrawlResult_Interface $crawlResult){
                 if(file_exists($file)){
                         $getID3 = $this->getID3Object();
                         getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);
                         $tagwriter = new getid3_writetags;

                         $tagwriter->filename       = $file;
                         $tagwriter->overwrite_tags = TRUE;
                         $tagwriter->tag_encoding   = "UTF-8";
                         $tagwriter->tagformats = array('id3v2.3');

                         //tagData
                         $tagData['title']               = array($crawlResult->getTitle());
                         $tagData['album']               = array($crawlResult->getRelease());
                         $tagData['artist']              = array($crawlResult->getArtist());
                         $tagData['remixer']             = array($crawlResult->getRemixer());
                         $tagData['composer']            = array($crawlResult->getProducer());
                         $tagData['band']                = array($crawlResult->getArtist());
                         $tagData['genre']               = array($crawlResult->getGenre());
                         $tagData['publisher']           = array($crawlResult->getLabel());
                         $tagData['year']                = array($crawlResult->getReleaseDate()->format('Y'));
                         $tagData['date']                = array($crawlResult->getReleaseDate()->format('dm'));
                         $tagData['subtitle']            = array($crawlResult->getMix());
                         $tagData['initial_key']         = array($crawlResult->getKey());
                         $tagData['beats_per_minute']    = array($crawlResult->getBpm());
                         $tagData['comment']             = array($crawlResult->getComment());
                         $tagData['recording_time']      = array($crawlResult->getReleaseDate()->format('Y')."-".$crawlResult->getReleaseDate()->format('d')."-".$crawlResult->getReleaseDate()->format('m'));

                         $tagData['attached_picture'][]=array(
                             'picturetypeid'=>2,
                             'description'=>'cover',
                             'mime'=>'image/jpeg',
                             'data'=> $crawlResult->getCoverData()
                         );


                         $tagwriter->tag_data = $tagData;

                         if($tagwriter->WriteTags()){
                                 return TRUE;
                         }
                         return FALSE;
                 }else{
                         throw new FileHelper_Exception("Die angegebene Datei ".htmlentities($file)." existiert nicht! ID3-Write abgebrochen!", 6);
                 }
         }

         /**
         *Getter for the found files counter
         *
         *@return int The number of all found files in the directory
         */
         public function getNumberOfFoundFiles(){
                 return $this->numberOfFoundFiles;
         }

         /**
         *Getter for the found mp3 files counter
         *
         *@return int The number of found mp3 files in the directory
         */
         public function getNumberOfFoundMp3Files(){
                 return $this->numberOfFoundMp3Files;
         }

         /**
         *Getter for the "allFiles"-Array (Array containing all the Files in the dir)
         */
         public function getAllFilesFromDir(){
                 if(empty($this->allFiles)){
                         $this->rescanFilesInDir();
                 }
                 return $this->allFiles;
         }

         /*
         *Method returns an getID3 object. Use this function to get new getID3 clones!
         *
         *@private
         *@returns GetID3
         */
         private function getID3Object(){
                 return clone $this->getID3;
         }
}

?>