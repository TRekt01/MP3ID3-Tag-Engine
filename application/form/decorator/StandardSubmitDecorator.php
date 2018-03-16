<?php
class Form_Decorator_standardSubmitDecorator extends Zend_Form_Decorator_Abstract
{

         public function buildInput()
         {
                 $element = $this->getElement();
                 $helper  = $element->helper;
                 return $element->getView()->$helper(
                         $element->getName(),
                         $element->getValue(),
                         $element->getAttribs(),
                         $element->options
                 );
         }

         public function render($content)
         {
                 $input  = $this->buildInput();

                 $output = "<td class='submit'>".$input."</td>";

                 return $output;
         }

}


?>