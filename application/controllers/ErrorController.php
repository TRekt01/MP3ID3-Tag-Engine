<?php

class ErrorController extends Zend_Controller_Action
{
         protected $errorLog;

         /**
         *Pre-Dispatch Method of the Edifact Action controller
         *Method will be called directly after the initialization
         *
         *@access public
         */
         public function preDispatch()
         {

         }

         /**
         *Initialization Method of the error controller.
         *Method will be called directly after the constructor. Please note: You cannot use the Constructor in the action classes.
         *For initialization stuff, use this method instead
         *
         *@access public
         */
         public function init()
         {
                 $this->errorLog = Zend_Registry::get('errorLog');
         }

         public function errorAction()
         {
                 $errors = $this->_getParam('error_handler');

                 $this->getResponse()->clearBody();
                 switch($errors->type){
                         case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                         case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                         case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                                 $this->getResponse()->setHttpResponseCode(404);

                                 $this->view->placeholder('HeadTitle')->append('Error 404!');
                                 $this->view->placeholder('SubTitle')->append('Die von Ihnen gesuchte Seite konnte nicht gefunden werden.');

                                 $this->view->topErrorText       = "<h3>Möglicherweise wurde die Seite gelöscht oder umbenannt, oder sie ist vorrübergehend nicht erreichbar.</h3>
                                                                   <u>Was Sie tun können:</u>
                                                                         <ul>
                                                                                 <li>Von der <a href=".$this->view->url(array('controller' => 'index')).">Startseite</a> manuell auf die gewünschte Seite Navigieren.</li>
                                                                                 <li>Überprüfen Sie Ihre Eingabe in der Adressleiste auf Tippfehler.</li>
                                                                                 <li>Falls Sie einem Link gefolgt sind, versuchen Sie es in ein paar minuten noch einmal.</li>
                                                                         </ul>
                                                                   ";
                                 $this->view->errorPicture       = "<img src='".URL."/images/oops404.jpg'/>";

                                 $this->view->bottomErrorText    = "<br />HTTP 404 - file not found - please check your entered URL - if you followed a link, please try again in a few minutes";

                                 $this->view->placeholder('HintsSidebar')->append("Überprüfen Sie die Eingabe in der Adressleiste Ihres Webbrowsers auf Tippfehler.");
                                 $this->view->placeholder('HintsSidebar')->append("Tritt der Fehler mehrmals auf? <a href=".$this->view->url(array('controller' => 'index', 'action' => 'kontakt')).">Setzen Sie sich bitte mit Ihrem Administrator in Verbindung.</a>");

                                 $this->view->dialogText = "<b><u>HTTP-Error: 404</u></b><br />Der Fehlercode 404 steht für \"Seite nicht gefunden\". <br />Vermutlich haben Sie sich vertippt oder sind einem abgelaufenem (altem) Link gefolgt. Versuchen Sie über die Startseite direkt auf die gewünschte Seite zu navigieren. Ggf. ist die gewünschte Seite auch nicht mehr verfügbar und wurde aus der Applikation entfernt. Gab es in letzter Zeit ein Update der Applikation? <br /><br /> Hilft alles nicht, <a href=".$this->view->url(array('controller' => 'index', 'action' => 'kontakt')).">setzen Sie sich bitte mit Ihrem Administrator in Verbindung.</a>";
                                 break;
                         default:
                                 $this->getResponse()->setHttpResponseCode(500);

                                 $this->view->placeholder('HeadTitle')->append('Error 500!');
                                 $this->view->placeholder('SubTitle')->append('Ein unerwarteter Fehler ist aufgetreten!');

                                 $this->view->topErrorText       = "<h3>Es kam zu einem internen Server-Fehler.</h3>
                                                                   <u>Was Sie tun können:</u>
                                                                         <ul>
                                                                                 <li><b>Prüfen Sie diese Fehlermeldung</b>:<br /> <span style='color: red;'>".$errors->exception->getMessage()."</span></li>
                                                                                 <li>Bitte beachten Sie, bestimmte Inhalte der Webseite können nur mit aktiviertem Javascript verwendet werden.</li>
                                                                                 <li>Überprüfen Sie ob Javascript aktiviert ist.</li>
                                                                                 <li>Falls Sie einem Link gefolgt sind, versuchen Sie es in ein paar Minuten noch einmal.</li>
                                                                         </ul>
                                                                   ";
                                 $this->view->errorPicture       = "<img src='".URL."/images/oops500.jpg'/>";
                                 $this->view->bottomErrorText = "HTTP 500 - Internal Server Error - the server encountered an internal error and was unable to complete your request.";

                                 $this->view->placeholder('HintsSidebar')->append("Tritt der Fehler mehrmals auf? <a href=".$this->view->url(array('controller' => 'index', 'action' => 'kontakt')).">Setzen Sie sich bitte mit Ihrem Administrator in Verbindung.</a>");

                                 $this->errorLog->log("\r\n"."Unexpected Exception (not catched in controller!) catched in ".__METHOD__."\r\n"."Some further informations: \r\nMESSAGE: ".$errors->exception->getMessage()." CODE: ".$errors->exception->getCode()." FILE: ".$errors->exception->getFile()." LINE: ".$errors->exception->getLine()."\r\n\r\nCOMPLETE:".$errors->exception."\r\n", Zend_Log::WARN);
                 }
         }
}

?>