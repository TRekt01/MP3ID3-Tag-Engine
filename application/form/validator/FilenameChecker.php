<?php
/**
*Validator checks a filename
*
*Any unallowed chars?
*
*/
class Form_Validate_filenameChecker extends Zend_Validate_Abstract
{
         private $pattern        = '^[<>?":|\/\\\\*]^';

         const INVALIDNAME       = 'ninvalid';

         protected $_messageTemplates = array(
                 self::INVALIDNAME => "This filename contains unallowed chars! Restricted chars: < > ? \" : | \ /*"
         );


         /**
         *Constructor
         *
         *@access public
         */
         public function __construct(){;
         }

         /**
         *Method checks, if the committed filename is valid
         *
         *@access public
         *@param mixed the comitted value
         *@return boolean TRUE if value is valid
         */
         public function isValid($value){

                 if( preg_match($this->pattern, $value, $match)){
                         $this->_error(self::INVALIDNAME);
                         return FALSE;
                 }

                 return TRUE;
         }

}
?>