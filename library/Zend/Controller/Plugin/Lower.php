<?php

class Zend_Controller_Plugin_Lower extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
         if(isset($this->_response))
         {

         }

    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
         if(isset($this->_response))
         {
                 $bdy    = $this->_response->getBody();
                 $bdy    = strtolower($bdy);
                 $this->_response->setBody($bdy);
         }

    }
}

?>