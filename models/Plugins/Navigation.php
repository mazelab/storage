<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Plugins_Navigation extends Zend_Controller_Plugin_Abstract
{
    
    /**
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if(!($identity = Zend_Auth::getInstance()->getIdentity()) ||
                !array_key_exists('group', $identity)) {
            return false;
        }
        
        if($identity['group'] === Core_Model_UserManager::GROUP_ADMIN) {
            $this->initAdminNavigation();
        } elseif ($identity['group'] === Core_Model_UserManager::GROUP_CLIENT) {
            $this->initClientNavigation();
        }
    }
    
    /**
     * init storage client navigation
     */
    public function initClientNavigation()
    {
        $naviPath = APPLICATION_PATH . '/../modules/mazelab/storage/configs/navigation/client.ini';
        if (file_exists($naviPath)) {
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->navigation()->addPages(new Zend_Config_Ini($naviPath));
        }
    }
    
    /**
     * init storage admin navigation
     */
    public function initAdminNavigation()
    {
        $naviPath = APPLICATION_PATH . '/../modules/mazelab/storage/configs/navigation/admin.ini';
        if (file_exists($naviPath)) {
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->navigation()->getContainer()->findBy('resource', 'dashboard')
                    ->addPages(new Zend_Config_Ini($naviPath));
        }
    }
    
}

