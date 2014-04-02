<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_ConfigController extends Zend_Controller_Action
{

    /**
     * use zend context switch for ajax requests
     */
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('clientconfig', array('json', 'html'))
                    ->addActionContext('nodeconfig', array('json', 'html'))
                    ->addActionContext('mainconfig', array('json', 'html'))
                    ->initContext();
    }

    public function clientconfigAction()
    {
    }

    public function mainconfigAction()
    {
    }

    public function nodeconfigAction()
    {
    }

    /**
     * sets forbidden http code in response object and disable rendering
     */
    public function setForbiddenStatus()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->getResponse()->setHttpResponseCode(403);
    }
}
