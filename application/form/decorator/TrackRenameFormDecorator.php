<?php
/**
*Create track rename form
*
*Class decorates and orders the elements and error-messages
*/
class Form_Decorator_trackRenameFormDecorator extends Zend_Form_Decorator_Abstract
{
         /**
         *Method configures the view-helper "formErrors". Its setting the start tags and the elements separator <div>foo <br/ > bar</div>
         */
         public function configureErrorView()
         {
                 $element        = $this->getElement();
                 $hlp            = $element->getView()->getHelper('FormErrors');

                 $hlp->setElementStart('<div class="smallerrordiv"><p>');
                 $hlp->setElementEnd('</p></div>');
                 $hlp->setElementSeparator('<br />');
         }
         /**
         *Method controlls the complete process of creating and decorating the form
         *
         *@return string the complete form in html
         */
         public function render($content)
         {

                 $form = $this->getElement();
                 $this->configureErrorView();

                 $allElements = $form->getElements();

                 $submit         = $form->getElement('rename');

                 $output         = "<form action='{$form->getAction()}' method='{$form->getMethod()}'>";

                 $output        .= "<table style='width: 100%; border: 1px solid #cdcdcd;'>";

                 foreach($allElements AS $track){
                         //Wenn es nicht submit ist, ausgeben
                         if($track->getName() != "rename"){
                                 $output         .= "<tr>".$track."</tr>";

                                 //hiddenfeld oder normales?
                                 if(substr($track->getName(), 0, 1) == "h"){
                                         $output         .= ($track->hasErrors()) ? "<tr><td colspan=2>".$track->getDecorator('standardElementDecorator')->buildErrors()."</td></tr>" : "";
                                 }else{
                                         $output         .= ($track->hasErrors()) ? "<tr><td colspan=2>".$track->getDecorator('trackRenameElementDecorator')->buildErrors()."</td></tr>" : "";
                                 }
                         }
                 }

                 $output        .= "</table>";

                 $output        .= "<tr>".$submit."</tr>";

                 $output        .= "</form>";

                 return $output;

         }
}
?>