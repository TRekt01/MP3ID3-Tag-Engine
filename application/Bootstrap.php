<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
         /**
         *Method sets ressources for filters, validators e.g. (+ loading)
         */
         public function _initRessourcesAndPlugins()
         {
                 $loader = new Zend_Loader_PluginLoader();

                 //XSS Filter laden (verwendung über Form_Filter_antiXss::filter())
                 $loader->addPrefixPath('Form_Filter', APPLICATION_PATH . '/form/filter', 'filter');
                 $loader->load('antiXss');
         }

         /**
         *Method sets the config objects and stores them in the registry
         */
         public function _initConfigObjects()
         {
                 //DB conf
                 $dbconf = new Zend_Config(require_once APPLICATION_PATH."/configs/database.php");
                 Zend_Registry::set('dbconf', $dbconf->database);

                 //App conf
                 $aconf  = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
                 Zend_Registry::set('appconf', $aconf);
         }

         /**
         *Method inits the database and stores them in the registry
         */
         public function _initDb()
         {
                 $db     = Zend_Db::factory(Zend_Registry::get('dbconf'));
                 Zend_Registry::set('db', $db);

                 Zend_Db_Table_Abstract::setDefaultAdapter($db);
         }

         /**
         *Method inits the session, configs it and stores it in the registry
         */
         public function _initSession()
         {
                 //to avoid "incomplete class error" when adding crawl results to the session... grml
                 require_once APPLICATION_PATH . '/models/crawlResult.php';

                 Zend_Session::setOptions(array("strict" => true));
                 Zend_Session::start();
                 Zend_Registry::set('session', new Zend_Session_Namespace('mp3writer'));
         }

         /**
         *Method sets the logs (normal Log and attack Log) and stores them in the registry
         */
         public function _initLog()
         {
                 $logger         = new Zend_Log();

                 $errorLog       = $this->getOption("errorLog");

                 if(empty($errorLog)){
                         throw new Exception('In der application.ini wurde kein Pfad zum error-Log definiert. Fügen Sie der application.ini einen Schlüssel "errorLog" und einen variablen Wert hinzu.', 2);
                 }

                 $writerTxtError         = new Zend_Log_Writer_Stream($errorLog);

                 $logger->addWriter($writerTxtError);

                 Zend_Registry::set('errorLog', $logger);

         }

         /**
         *Method sets the MP3-DIR-Information from the Application.ini
         */
         public function _initMp3Dir(){
                 $mp3dir = $this->getOption("mp3dir");

                 if(empty($mp3dir)){
                         throw new Exception('In der applikation.ini wurde kein Storage-Pfad für die MP3-Dateien definiert! Fügen Sie der application.ini einen Schlüssel "mp3dir" und einen variablen Wert (Pfadangabe) hinzu.', 3);
                 }

                 Zend_Registry::set('mp3dir', $mp3dir);
         }

         /**
         *Method sets the ID3-Comment-Value from the Application.iní
         */
         public function _initId3Comment(){
                 $id3Comment     = $this->getOption("id3Comment");

                 if(empty($id3Comment)){
                         throw new Exception('In der applikation.ini wurde kein ID3-Commnet für die MP3-Dateien definiert! Fügen Sie der application.ini einen Schlüssel "id3Comment" und einen Variablen Wert (Comment) hinzu.', 4);
                 }

                 Zend_Registry::set('id3Comment', $id3Comment);
         }

         /**
         *Method starts the MVC
         */
         public function _initLayout()
         {
                 Zend_Layout::startMvc();
         }

         /**
         *Method inits the view-ressource
         */
         public function _initView()
         {
                 $view   = new Zend_View();
                 $view->setEncoding('ISO-8859-1');
                 //JQuery action helper laden
                 $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
                 $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
                 $viewRenderer->setView($view);
                 Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

                 return $view;
         }

         /**
         *Method loads the css files
         */
         public function _initStylesheets()
         {
                 $this->bootstrap('View');
                 $view = $this->getResource('View');
                 $view->headLink()->prependStylesheet('/style.css');
         }

         /**
         *Method inits the front controller and commits the action-controller-directorys
         */
         public function _initController()
         {
                 $zfc     = Zend_Controller_Front::getInstance();
                 $zfc->setControllerDirectory(APPLICATION_PATH . '/controllers');
                 $res     = $zfc->getResponse();
                 $req     = $zfc->getRequest();
         }

         /**
         *Method inits the sidebars html
         */
         public function _initSidebar()
         {
                 $view = $this->getResource('View');


                 $view->placeholder('LinksSidebar')
                                                 ->setPrefix("<ul>")
                                                 ->setSeparator("</li>\n <li>\n ")
                                                 ->setPostfix("</ul>");

                 $view->placeholder('HintsSidebar')
                                                 ->setPrefix("<ul>\n <li>\n")
                                                 ->setSeparator("</li>\n <li>\n ")
                                                 ->setPostfix("</li>\n</ul>");
         }

         /**
         *Method inits the top links html
         */
         public function _initTopLinks()
         {
                 $view   = $this->getResource('View');

                 $view->placeholder('Toplinks')
                                                 ->setPrefix('<table class="loggedInLinks"><tr><td>')
                                                 ->setSeparator("</td><td>")
                                                 ->setPostfix("</td></tr></table>");
         }

         /**
         *Method loads the sitename from the application.ini, filters and sets it
         */
         public function _initSitename()
         {
                 $sitename       = $this->getOption("sitename");

                 if(empty($sitename)){
                         throw new Exception('In der application.ini wurde kein Sitename definiert. Fügen Sie der application.ini einen Schlüssel "sitename" und einen Wert (string) hinzu.', 12);
                 }

                 $sitename = Form_Filter_antiXss::filter($sitename);

                 $view   = $this->getResource('View');
                 $view->headTitle($sitename)->setSeparator('::');

                 Zend_Registry::set('sitename', $sitename);
         }

         /**
         *Method inits all needed constants
         */
         public function _initConstants()
         {
                 @define('URL', 'http://'.$_SERVER['HTTP_HOST']);
                 @define('INIPATH', APPLICATION_PATH . '/configs/application.ini');
         }

         /**
         *Method sets the language of standard zend-framework-error-messages to german
         */
         public function _initErrorMessageLanguage()
         {
                 $translator = new Zend_Translate(
                         array(
                                 'adapter' => 'array',
                                 'content' => APPLICATION_PATH . '/../library/resources/languages',
                                 'locale' => 'de_DE',
                                 'scan' => Zend_Translate::LOCALE_DIRECTORY
                         )
                 );
                 Zend_Validate_Abstract::setDefaultTranslator($translator);
         }

         /**
         *Method inits the id3 library
         */
         public function _initGetid3(){
                 require_once APPLICATION_PATH . '/../library/Getid3/getid3.php';

                 $getid3 = new getID3();

                 Zend_Registry::set('getID3', $getid3);
         }
}

?>