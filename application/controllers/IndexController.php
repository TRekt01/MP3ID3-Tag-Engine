<?php

//WANDELT ALLE ERRORS IN EINE EXCEPTION UM !
function warning_handler($errno, $errstr) {
         throw new Exception();
}

class IndexController extends Zend_Controller_Action
{
         private $errorLog = "";

         public function init()
         {
                 $this->view->render('index/NormalSidebar.phtml');
                 $this->errorLog = Zend_Registry::get('errorLog');

                 $ajaxContext = $this->_helper->getHelper('AjaxContext');
                 $ajaxContext->addActionContext('ajaxid3crawl', 'json')->initContext();
                 $ajaxContext->addActionContext('ajaxid3write', 'json')->initContext();
         }

         public function preDispatch()
         {

         }

         public function postDispatch()
         {
                 //Code wird NACH dem Ausführen einer AKTION ausgeführt
         }

         /**
         *Returns the form for track name editing
         *
         *@access public
         *@param array Array with the Tracks. One input field for each.
         *@return Zend_Form the form
         */
         public function getTrackRenameForm($tracks){
                 foreach($tracks AS $key => $track){
                         $helper = pathinfo($track);
                         $trackNames[$key]["filename"] = $helper['filename'];
                         $trackNames[$key]["path"] = $track;
                 }

                 $form   = new Zend_Form();
                 $form->addPrefixPath('Form_Decorator', '../application/form/decorator', 'decorator');
                 $form->addDecorator('trackRenameFormDecorator');

                 $form->addElementPrefixPath('Form_Filter', '../application/form/filter', 'filter');
                 $form->addElementPrefixPath('Form_Validate', '../application/form/validator', 'validate');

                 $form->setAttribs(array('id' => 'renameTracksId', 'name' => 'renameTracksName', 'action' => '/index/index/', 'method' => 'POST'));

                 $counter = 1;

                 foreach($trackNames AS $track){
                         $form->addElement('hidden', "htrack".$counter , array('value' => $track["path"], 'decorators' => array('standardElementDecorator'), 'filters' => array('antiXss')));
                         $form->getElement("htrack".$counter)->addValidator('inArray', true, array("key" => $track["path"], "messages" => "Please do not manipulate any form field! Please reload Applikation!"));

                         //Input fields for tracks
                         $form->addElement('text', "track".$counter,     array( 'attribs' => array("id" => "trackId".$counter, "class" => "input_extralong", "title" => $track["filename"]),
                                                                         'label' => $counter.". Track:",
                                                                         'decorators' => array('trackRenameElementDecorator'),
                                                                         'filters' => array('antiXss'),
                                                                         'required' => TRUE
                                                                 ));
                         $form->getElement("track".$counter)->addValidator('notEmpty', true, array("messages" => "Please declare track name!"));
                         $form->getElement("track".$counter)->addValidator('filenameChecker', TRUE);
                         $form->getElement("track".$counter)->setValue($track["filename"]);
                         $counter++;
                 }

                 //submit
                 $form->addElement('submit', 'rename', array('value' => 'rename and continue', 'decorators' => array('standardSubmitDecorator')));

                 return $form;

         }

         /**
         *Method creates the form and (nearly) all elements that are needed for searching and writing of id3 tags
         *Heavy DOM-Manipulation with JQUERY XHR
         *
         *The Form will never be executed with a submit button or something - everything is going completely with buttons and XMLHTTP-Requests.
         *No execute = no zend filter or validate = no need of Zend_Form anymore
         *
         *@access public
         *@param array Needs an array with this schematic: [trackname] => path
         *@return String The complete Form
         */
         public function getID3TagSearchForm($mp3s){
                 $form   = "<form action='' method='POST'>";
                 $form  .= "<table style='width: 100%; border: 1px solid #cdcdcd;'>";
                 $counter = 1;
                 foreach($mp3s AS $filename){
                         //show search value element
                         $form   .= "<tr>";
                         $form   .= "<td id=\"td".$counter."\"><td><input type=\"text\" name=\"".$filename."\" id=\"input".$counter."\" class=\"input_extralong\" value=\"".basename($filename, ".mp3")."\"><input type=\"button\" class=\"search\" id=\"".$counter."\" value=\"Search ID3-Tags\"></td>";
                         $form   .= "</tr>";

                         //Show the resultdiv!
                         $form   .= "<tr><td></td><td><div id=\"resultdiv".$counter."\" style=\"border: 1px dashed silver\"></div></td></tr>";

                         $counter++;
                 }
                 $form   .= "</table>";
                 $form   .= "</form>";
                 return $form;
         }

         public function indexAction()
         {
                 try{
                         require_once APPLICATION_PATH . '/models/FileHelper.php';

                         $FileHelper      = new FileHelper(Zend_Registry::get('mp3dir'), Zend_Registry::get('getID3'));
                         $allFiles       = $FileHelper->getAllFilesFromDir();
                         $mp3Files       = $FileHelper->getMp3FilesFromDir(FALSE);

                         $this->view->allFiles   = $FileHelper->getNumberOfFoundFiles();
                         $this->view->mp3Files   = $FileHelper->getNumberOfFoundMp3Files();

                         if(!empty($mp3Files)){
                                 $form   = $this->getTrackRenameForm($mp3Files);
                                 $this->view->renameForm = $form;

                                 if($this->getRequest()->isPost()){
                                         if(!$form->isValid($_POST)){
                                                 $this->view->renameForm = $form;
                                                 return $form->render();
                                         }

                                         $values = $form->getValidValues($_POST);

                                         //Pfad und neuer Filename grabben und rename
                                         $noErrors       = TRUE;
                                         foreach($values AS $key => $value){
                                                 if(substr($key, 0, 5) == "track"){
                                                         if(!empty($value)){
                                                                 $newFileName = $value;

                                                                 if(isset($values["h".$key])){
                                                                         $oldFileName       = $values["h".$key];
                                                                         if(!empty($oldFileName)){
                                                                                 try{
                                                                                         FileHelper::renameFile(Zend_Registry::get('mp3dir'), $oldFileName, $newFileName.".mp3");
                                                                                 }catch(FileHelper_Exception $e){
                                                                                         $noErrors       = FALSE;
                                                                                         continue;
                                                                                 }
                                                                         }
                                                                 }
                                                         }
                                                 }
                                         }

                                         if($noErrors){
                                                 $this->view->renameForm = $this->view->render('FailureAndCompleted/renameCompleted.phtml');
                                         }else{
                                                 $this->view->renameForm = $this->view->render('FailureAndCompleted/renameCompletedWithErrors.phtml');
                                         }
                                 }
                         }else{
                                 $this->view->dir        = Zend_Registry::get('mp3dir');
                                 $this->view->renameForm = $this->view->render('FailureAndCompleted/noFilesFoundFailure.phtml');
                         }
                 }catch(Exception $e){
                         echo "<pre>";
                                 print_r($e);
                         echo "</pre>";
                         $this->errorLog->log("\r\n"."Exception catched in ".__METHOD__."\r\n"."Some further informations: \r\nMESSAGE: ".$e->getMessage()." CODE: ".$e->getCode()." FILE: ".$e->getFile()." LINE: ".$e->getLine()."\r\n", Zend_Log::WARN);
                 }

         }

         public function steptwoAction(){
                 try{
                         //first, the session variable "crawlResults" have to be killed. if not, all results will be store in the session until the users quits the application
                         Zend_Registry::get('session')->crawlResults = "";

                         require_once APPLICATION_PATH . '/models/FileHelper.php';

                         $fh = new FileHelper(Zend_Registry::get('mp3dir'), Zend_Registry::get('getID3'));

                         //Hole alle MP3-Files und combine diese mit dem Filename (für search!)
                         $mp3s = $fh->getMp3FilesFromDir();

                         if(!empty($mp3s)){
                                 $this->view->tagSearchWriteForm = $this->getID3TagSearchForm($mp3s);
                         }else{
                                 $this->view->dir                = Zend_Registry::get('mp3dir');
                                 $this->view->tagSearchWriteForm = $this->view->render('FailureAndCompleted/noFilesFoundFailure.phtml');
                         }
                 }catch(Exception $e){
                         echo "<pre>";
                                 print_r($e);
                         echo "</pre>";
                         $this->errorLog->log("\r\n"."Exception catched in ".__METHOD__."\r\n"."Some further informations: \r\nMESSAGE: ".$e->getMessage()." CODE: ".$e->getCode()." FILE: ".$e->getFile()." LINE: ".$e->getLine()."\r\n", Zend_Log::WARN);
                 }
         }


         public function settingsAction(){

         }

         public function ajaxid3crawlAction()
         {
                 try{
                         if($this->getRequest()->isXmlHttpRequest()){

                                 $jsonObject     = Zend_Json::decode($this->getRequest()->getRawBody(), Zend_Json::TYPE_OBJECT);
                                 $trackname      = Form_Filter_antiXss::filter(utf8_decode($jsonObject->Trackname));
                                 $filename       = Form_Filter_antiXss::filter(utf8_decode($jsonObject->Filename));

                                 if(!empty($trackname) && !empty($filename)){
                                         require_once APPLICATION_PATH . '/models/CrawlResultFactory.php';
                                         require_once APPLICATION_PATH . '/models/BeatportProV1.php';

                                         $beatport       = new BeatportProV1(new CrawlResultFactory());

                                         $crawlResults   = $beatport->searchId3Tags($trackname, TRUE);



                                         if(empty($crawlResults)){
                                                 $this->getHelper('json')->sendJson(FALSE);
                                                 exit;
                                         }else{
                                                 $results = array();
                                                 $helper = array();

                                                 //wirte Comment, build result array for json returne && store in helper array for session
                                                 foreach($crawlResults AS $result){
                                                         $result->setComment(Zend_Registry::get('id3Comment'));
                                                         $id     = str_shuffle(md5(time()).mt_rand(1, 99999));
                                                         $results[$id] = $result->getCompleteDataAsArray();
                                                         $helper[$id] = $result;
                                                 }
                                         }


                                         //Store the hits in the session. They will be dropped out after refresh or he specific array after a id3-write
                                         $session = Zend_Registry::get('session');
                                         $allResults = $session->crawlResults;
                                         if(empty($allResults)){
                                                 $allResults = array();
                                         }
                                         $allResults[$filename]   = $helper;
                                         $session->crawlResults = $allResults;

                                         $this->getHelper('json')->sendJson($results);
                                 }else{
                                         $this->getHelper('json')->sendJson(FALSE);
                                         exit;
                                 }
                         }else{
                                 $this->_helper->redirector->gotoSimple('steptwo');
                         }
                 }catch(Exception $e){
                         $this->errorLog->log("\r\n"."Exception catched in ".__METHOD__."\r\n"."Some further informations: \r\nMESSAGE: ".$e->getMessage()." CODE: ".$e->getCode()." FILE: ".$e->getFile()." LINE: ".$e->getLine()."\r\n", Zend_Log::WARN);
                         $this->getHelper('json')->sendJson(FALSE);
                 }
         }

         public function ajaxid3writeAction(){
                 try{    set_error_handler("warning_handler", E_WARNING);
                         if($this->getRequest()->isXmlHttpRequest()){
                                 $jsonObject     = Zend_Json::decode($this->getRequest()->getRawBody(), Zend_Json::TYPE_OBJECT);
                                 $resultId       = Form_Filter_antiXss::filter(utf8_decode($jsonObject->ResultId));
                                 $filename       = Form_Filter_antiXss::filter(utf8_decode($jsonObject->Filename));

                                 if(!empty($resultId) && !empty($filename)){
                                         $session = Zend_Registry::get('session');
                                         $allResults = $session->crawlResults;

                                         if(empty($allResults) || empty($allResults[$filename]) || empty($allResults[$filename][$resultId]) || !($allResults[$filename][$resultId] instanceof CrawlResult_Interface)){
                                                 $this->getHelper('json')->sendJson(FALSE);
                                                 exit;
                                         }else{
                                                 //crawl result holen (klonieren)
                                                 $crawlResult    = clone $allResults[$filename][$resultId];

                                                 //speicher aus der Session freigeben!
                                                 unset($allResults[$filename]);

                                                 require_once APPLICATION_PATH . '/models/FileHelper.php';
                                                 $fh = new FileHelper(Zend_Registry::get('mp3dir'), Zend_Registry::get('getID3'));

                                                 if($fh->writeId3Data(Zend_Registry::get('mp3dir')."/".$filename, $crawlResult)){
                                                         $this->getHelper('json')->sendJson(TRUE);
                                                 }else{
                                                         $this->getHelper('json')->sendJson(FALSE);
                                                 }
                                         }
                                 }else{
                                         $this->getHelper('json')->sendJson(FALSE);
                                         exit;
                                 }
                         }else{
                                 $this->_helper->redirector->gotoSimple('steptwo');
                         }
                 }catch(Exception $e){
                         $this->errorLog->log("\r\n"."Exception catched in ".__METHOD__."\r\n"."Some further informations: \r\nMESSAGE: ".$e->getMessage()." CODE: ".$e->getCode()." FILE: ".$e->getFile()." LINE: ".$e->getLine()."\r\n", Zend_Log::WARN);
                         $this->getHelper('json')->sendJson(FALSE);
                 }
         }
}
?>