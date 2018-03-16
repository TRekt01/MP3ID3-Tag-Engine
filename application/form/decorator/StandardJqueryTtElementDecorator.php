<?php
/**
*Standard Jqeruy Tooltip Element decorator
*
*Use this decorator in combination with Jquery. This decorator uses the jquery tooltip element functionality (title="")
*Commit an text as option "Tooltip" !!!
*
*Class decorates the element (creating labels, building error-messages).
*Label and Element will be enframed by <td></td> elemnts (<td>label:</td><td>element</td>
*/
class Form_Decorator_standardJqueryTtElementDecorator extends Zend_Form_Decorator_Abstract
{
         /**
         *Method creates the elements label
         *
         *@param boolean True, if element has errors (font color!)
         *@return string the created html element (or empty string if no label was speficied)
         */
         public function erstelleLabel($error)
         {
                 $element        = $this->getElement();
                 $label          = $element->getLabel();

                 if(!empty($label)){
                         if($error === TRUE){
                                 return $element->getView()->formLabel($element->getName(), $label, array("style" => "color: #CD0000"));
                         }else{
                                 return $element->getView()->formLabel($element->getName(), $label);
                         }
                 }
                 return $label;
         }

         /**
         *Method build the input-fiels
         *
         *@return string the input-fiel (html element)
         */
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

         /**
         *Method gets elements errors, build an returns it
         *Method takes all error messages of an element (may be more than one!) and reduce it to one error message (the first error registred).
         *So that only one error message is displayed at the same time
         *
         *@return string the html-element which contains the single error message
         */
         public function buildErrors()
         {
                 $element        = $this->getElement();
                 $msg            = $element->getMessages();
                 $msg            = array(array_pop($msg));

                 $error = (! $msg) ? "" : $element->getView()->formErrors($msg);

                 return $error;
         }

         /**
         *Render method controls the process of creating the element
         *
         *@return string the complete element (with errors!) in html
         */
         public function render($content)
         {
                 $element = $this->getElement();
                 if (!$element instanceof Zend_Form_Element){
                         return $content;
                 }
                 if (null === $element->getView()){
                         return $content;
                 }

                 if($element->hasErrors()){
                         $element->setAttrib("style","background: #ffeeee");
                         $label     = $this->erstelleLabel(TRUE);
                 }else{
                         $label     = $this->erstelleLabel(FALSE);
                 }

                 $input     = $this->buildInput();
                 if($label != ""){
                         $output = "<td title='".$this->getOption('Tooltip')."'>".$label."</td><td>".$input."</td>";
                 }else{
                         $output = "<td title='".$this->getOption('Tooltip')."'>".$input."</td>";
                 }
                 return $output;
         }
}
?>