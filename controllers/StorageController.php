<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_StorageController extends Zend_Controller_Action
{
    
    /**
     * message when given storage was not found
     */
    CONST MESSAGE_NOT_FOUND = 'Storage not found...';
    
    /**
     * set not found header
     */
    protected function _setNotFoundHeader()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->getResponse()->setHttpResponseCode(404);
    }
    
    /**
     * set pager with existing context in view
     * 
     * @param string $clientId
     */
    protected function _setPager($clientId = null)
    {
        $pager = MazelabStorage_Model_DiFactory::newStorageSearch($clientId);
        $pager->setLimit($this->getParam('limit', 10));
        if($this->getParam('term')) {
            $pager->setSearchTerm($this->getParam('term'));
        }
        
        if($this->getParam('pagerAction') == 'last') {
            $pager->last();
        } else {
            $pager->setPage($this->getParam('page', 1))->page();
        }
        
        $this->view->pager = $pager->asArray();
    }

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('delete', 'json')
                    ->addActionContext('enable', 'json')
                    ->addActionContext('disable', 'json')
                    ->addActionContext('editadmin', 'json')
                    ->addActionContext('editclient', 'json')
                    ->addActionContext('indexclient', 'html')
                    ->addActionContext('indexadmin', 'html')
                    ->initContext();
        
        // set view messages from MessageManager
        $this->_helper->getHelper("SetDefaultViewVars");
    }
    
    public function enableAction()
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        if(!($storageId = $this->getParam('storageId')) || !$storageManager->getStorageAsArray($storageId)) {
            return $this->_setNotFoundHeader();
        }
        
        $this->view->result = $storageManager->enableStorage($storageId);
    }
    
    public function addAction()
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        $form = new MazelabStorage_Form_Add();
        
        if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            if(($storageId = $storageManager->addStorage($form->getValues()))) {
                $redirector = $this->_helper->getHelper('Redirector');
                $redirector->gotoRoute(array($storageId), "mazelab-storage_edit");
            }
        }
        
        $this->view->form = $form;
    }
    
    public function disableAction()
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        if(!($storageId = $this->getParam('storageId')) || !$storageManager->getStorageAsArray($storageId)) {
            return $this->_setNotFoundHeader();
        }
        
        $this->view->result = $storageManager->disableStorage($storageId);
    }
    
    public function deleteAction()
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        if(!($storageId = $this->getParam('storageId')) || !$storageManager->getStorageAsArray($storageId)) {
            return $this->_setNotFoundHeader();
        }
        
        $this->view->result = $storageManager->flagDelete($storageId);
    }
    
    public function editAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (array_key_exists('group', $identity) &&
                $identity['group'] == Core_Model_UserManager::GROUP_ADMIN) {
            return $this->forward('editadmin');
        }

        $this->forward('editclient');
    }
    
    public function editadminAction()
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        $storageId = $this->getParam('storageId');
        
        if(!($storage = $storageManager->getStorageAsArray($storageId))) {
            Core_Model_DiFactory::getMessageManager()->addError(self::MESSAGE_NOT_FOUND);
            return $this->forward('index');
        }
        
        $form = new MazelabStorage_Form_Edit();
        if($this->getRequest()->isPost() && ($post = $this->getRequest()->getPost())) {
            if($form->isValidPartial($post)) {
                $this->view->result = $storageManager->updateStorage($storageId, $post);
            } else {
                $this->view->result = false;
                Core_Model_DiFactory::getMessageManager()->addZendFormErrorMessages($form);
            }
        }
        
        $this->view->form = $form->setDefaults($storage);
        $this->view->storage = $storage;
    }
    
    public function editclientAction()
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $storageId = $this->getParam('storageId');

        if(!($storage = $storageManager->getStorageAsArray($storageId, $identity['_id']))) {
            Core_Model_DiFactory::getMessageManager()->addError(self::MESSAGE_NOT_FOUND);
            return $this->forward('index');
        }
        
        $form = new MazelabStorage_Form_EditClient();
        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $this->view->result = $storageManager
                        ->updateStorage($storageId, $form->getValues());
            } else {
                Core_Model_DiFactory::getMessageManager()
                        ->addZendFormErrorMessages($form);
            }
        }
        
        $this->view->form = $form->setDefaults($storage);
        $this->view->storage = $storage;
    }
    
    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (array_key_exists('group', $identity) &&
                $identity['group'] === Core_Model_UserManager::GROUP_ADMIN) {
            return $this->forward('indexadmin');
        }

        $this->forward('indexclient');
    }
    
    public function indexadminAction()
    {
        $this->_setPager();
    }
    
    public function indexclientAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->_setPager($identity['_id']);
    }
    
}
