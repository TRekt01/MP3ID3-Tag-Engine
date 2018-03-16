<?php
/**
*Own filter class for zend framework form stuff. Checks a element for xss attacks and filters complete
*HTML and critical chars out of the string!
*
*@author Timo Autenrieth
*/
class Form_Filter_antiXss implements Zend_Filter_Interface
{
         public function filter($value)
         {
                 if($value){
                         $charset = "ISO-8859-1";
                         $value = htmlentities($value, ENT_QUOTES, $charset);

                         $filteredAndReplacedValue       = str_replace(array("&Auml;","&auml;","&Ouml;", "&ouml;", "&Uuml;", "&uuml;" ,"&amp;", "&szlig;", "&#039;"), array("Ä", "ä", "Ö", "ö", "Ü", "ü", "&", "ß", "'"), $value);

                         return $filteredAndReplacedValue;
                 }
                 return $value;
         }
}

?>