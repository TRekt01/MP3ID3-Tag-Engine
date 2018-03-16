<?php
/**
*Own filter class for zend framework form stuff. Clears leading zeros of any given value
*
*@author Timo Autenrieth
*/
class Form_Filter_leadingZeroKiller implements Zend_Filter_Interface
{
         public function filter($value)
         {
                 if($value){
                         $strlen = strlen($value);
                         if($strlen > 1){
                                 $helper = str_split($value);

                                 foreach($helper AS $key => $char){
                                         if($char == "0"){
                                                 unset($helper[$key]);
                                         }else{
                                                 break;
                                         }
                                 }
                                 $filteredAndReplacedValue = "";
                                 foreach($helper AS $v){
                                         $filteredAndReplacedValue .= $v;
                                 }

                                 return $filteredAndReplacedValue;
                         }else{
                                 return $value;
                         }
                 }
                 return NULL;
         }
}

?>