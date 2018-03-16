<?php
/**
*View Helper Class
*
*Takes an substring and the original string and highlights the substring in the original string
*seach Value=abCD, found value = Abcdef. New value will be Ab<span style='font-size:130%; font-weight: bold; color: $val;'>cd</span>ef
*/
class View_Helper_GenerateFat extends Zend_View_Helper_Abstract{

         /**
         *Method highlights an substring (e.g. search value typed in by a user) in the original string (maybe value from the database).
         *Method will not just "fat" the search value and replace it, because an seach Value like "abCDEfg" would end up in an highlighted "abCDEFg..."
         *although the original value is "Abcdefg". Method "fats" an substring of the original string.
         *
         *Helper can also be used to highlight multiple small parts of a strange search values like "im versor biet 12"
         *Just split the seach string in its parts (im, versor, biet, 12) and commit them with the original value (im versogrungsgebiet 12) sperately
         *to the helper.
         *
         *@acesss public
         *@param string the substring (search value)
         *@param string the original value (e.g. from a database)
         *@param string html color code used for highlighting
         *@return string a highlighted string
         */
         public function generateFat($searchValue, $originalValue, $highlightColor){
                 //Method starts highlighting after the last html element (needed if a search value is splittet in single parts which are highlighted with html tags).
                 //If method wouldn't start after last html, html tags would be highlighted if short search values commited (e.g. im vers e biet => highlighting the "e" would end up in a highlighted <span stylE <--)
                 if($this->hasStringHtml($originalValue)){
                         $posOfLastHtmlElement   = strripos($originalValue, ">")+1;

                         $subWithHtml    = substr($originalValue, 0, $posOfLastHtmlElement);
                         $subWithoutHtml = substr($originalValue, $posOfLastHtmlElement);

                         $pos    = stripos($subWithoutHtml, $searchValue);
                         $len    = strlen($searchValue);
                         $suborg = substr($subWithoutHtml, $pos, $len);

                         $highlightedValue       = substr_replace($subWithoutHtml, "<span style='font-size:130%; font-weight: bold; color: ".$highlightColor.";'>".$suborg."</span>", $pos, $len);
                         $highlightedValue       = $subWithHtml.$highlightedValue;
                 }else{
                         $pos    = stripos($originalValue, $searchValue);
                         if($pos === 0 || $pos > 0){
                                 $len    = strlen($searchValue);
                                 $suborg = substr($originalValue, $pos, $len);

                                 $highlightedValue       = substr_replace($originalValue, "<span style='font-size:130%; font-weight: bold; color: ".$highlightColor.";'>".$suborg."</span>", $pos, $len);
                         }else{
                                 $highlightedValue       = $originalValue;
                         }
                 }

                 return $highlightedValue;
         }


         /**
         *Method checks, if an string contains html values
         *
         *@access private
         *@param string the string
         *@return boolean TRUE if string contains html
         */
         private function hasStringHtml($checkString){

                 if($checkString !== strip_tags($checkString)){
                         return TRUE;
                 }
                 return FALSE;
         }
}
?>