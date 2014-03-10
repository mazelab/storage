<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Apply_Storage
{
    
    /**
     * @var MazelabStorage_Model_ValueObject_Storage
     */
    protected $_storage;

    /**
     * gets current storage instance
     * 
     * @return MazelabStorage_Model_ValueObject_Storage
     */
    protected function _getStorage()
    {
        return $this->_storage;
    }
    
    /**
     * gets commands in order to achieve desired state
     * 
     * @return array|null
     */
    protected function _getCommands()
    {
        if($this->_getStorage()->getData('delete') === true) {
            return $this->_getCommandsDelete();
        }
        
        // no conflicts then do nothing and manuell conflicts must be resolved before
        if(!$this->_getStorage()->getConflicts() || 
                $this->_getStorage()->getConflicts(MazeLib_Bean::STATUS_MANUALLY)) {
            return null;
        }
        
        return $this->_getCommandsSet();
    }
    
    /**
     * get delete commands for current storage
     * 
     * @return array
     */
    protected function _getCommandsDelete()
    {
        $commands[] = "client {$this->_getEscapedName()} delete";
        return $commands;
    }
    
    /**
     * get deactivate commands for current storage
     * 
     * @return array
     */
    protected function _getCommandsSet()
    {
        if(!($quota = $this->_getEscapedQuota()) || !($pw = $this->_getEscapedPassword()) ||
                !($name = $this->_getEscapedName())) {
            return array();
        }
        
        if(!$this->_getStorage()->getStatus()) {
            $commands[] = "client -Q {$quota} -P {$pw} -d {$name} set";
        } else {
            $commands[] = "client -Q {$quota} -P {$pw} {$name} set";
        }
        
        return $commands;
    }
    
    /**
     * return escaped storage name string for shell use
     * 
     * @return string|null
     */
    protected function _getEscapedName()
    {
        if(!($name = $this->_getStorage()->getName())) {
            return null;
        }
        
        return escapeshellarg($name);
    }
    
    /**
     * return escaped storage password string for shell use
     * 
     * @return string|null
     */
    protected function _getEscapedPassword()
    {
        if(!($password = $this->_getStorage()->getData('password'))) {
            return null;
        }
        
        return escapeshellarg($password);
    }
    
    /**
     * return escaped quota string for shell use
     * 
     * @return string|null
     */
    protected function _getEscapedQuota()
    {
        if(!($quota = $this->_getStorage()->getData('quota'))) {
            return null;
        }
        
        return escapeshellarg($quota);
    }
    
    /**
     * apply given storage instance
     * 
     * @param MazelabStorage_Model_ValueObject_Storage
     * @param boolean $save (default true) save or only set commands
     * @return boolean
     */
    public function apply(MazelabStorage_Model_ValueObject_Storage $storage, $save = true)
    {
        $this->_storage = $storage;
        if(!($node = $this->_getStorage()->getNode())) {
            return false;
        }
        
        if(!($commands = $this->_getCommands())) {
            return true;
        }
        
        $key = "storage {$this->_getStorage()->getId()}";
        if(($result = $node->getCommands()->addContextCommands('storage', $key, $commands)) && $save) {
            return $node->getCommands()->save();
        }
        
        return $result;
    }
    
    /**
     * remove commands from current node
     * 
     * @param $storage MazelabStorage_Model_ValueObject_Storage
     * @return boolean MazelabStorage_Model_ValueObject_Storage
     */
    public function remove(MazelabStorage_Model_ValueObject_Storage $storage)
    {
        $this->_storage = $storage;
        if(!($node = $this->_getStorage()->getNode()) || !($commands = $node->getCommands())) {
            return true;
        }
        
        $key = "storage {$this->_getStorage()->getId()}";
        if(!$commands->addContextCommands(MazelabStorage_Model_StorageManager::MODULE_NAME, $key, array())) {
            return false;
        }
        
        return $commands->save();
    }
    
}
