<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Plugins_Layout extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called after Zend_Controller_Router exits.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown($request)
    {
        if ($request->getModuleName() == "mazelab-storage"){
            $view = Zend_Layout::getMvcInstance()->getView();
//            $view->headLink()->prependStylesheet("/module/mazelab/storage/css/default.css");
            
            if(($identity = Zend_Auth::getInstance()->getIdentity()) && array_key_exists('group', $identity) &&
                    $identity['group'] === Core_Model_ClientManager::GROUP_CLIENT) {
                $view->headLink()->prependStylesheet("/module/mazelab/storage/css/client.css");
            }
        }
    }

}