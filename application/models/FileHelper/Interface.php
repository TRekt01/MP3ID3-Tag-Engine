<?php
/**
*File helper interface
*/
interface FileHelper_Interface{
         public function getAllFilesFromDir();
         public function getMp3FilesFromDir($withDirInfo = FALSE);
         public function rescanFilesInDir();
         public static function renameFile($directory, $oldFileName, $newFileName);
         public function getNumberOfFoundFiles();
         public function getNumberOfFoundMp3Files();
         public function writeId3Data($pathToFile, CrawlResult_Interface $cR);
}
?>