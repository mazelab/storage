<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /**
     * init the acl of your module
     */
    protected function _initAcl()
    {
        $aclPath = __DIR__ . '/configs/acl.ini';

        if (file_exists($aclPath)) {
            // use own acl builder
            $acl = Zend_Registry::getInstance()->get('MazeLib_Acl_Builder');
            $acl->addConfig(new Zend_Config_Ini($aclPath));
        }
    }

    protected function _initTranslate()
    {
        $translations = $aclPath = __DIR__ . '/data/locales/';
        if (file_exists($translations) && Zend_Registry::getInstance()->isRegistered("Zend_Translate")){
            $translate = Zend_Registry::getInstance()->get("Zend_Translate");
            $translate->getAdapter()->addTranslation($translations);
        }
    }

    protected function _initPlugins()
    {
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        
        $front->registerPlugin(new MazelabStorage_Model_Plugins_Navigation())
              ->registerPlugin(new MazelabStorage_Model_Plugins_Layout())
              ->registerPlugin(new MazelabStorage_Model_Plugins_Events());
    }
    
    /**
     * init the routes of your module
     */
    public function _initRouter(array $options = null)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $routingFile = __DIR__ . '/configs/routes.ini';

        if (file_exists($routingFile)) {
            $router->addConfig(new Zend_Config_Ini($routingFile, $this->getEnvironment()), 'routes');
        }
        
        return $router;
    }
    
}
