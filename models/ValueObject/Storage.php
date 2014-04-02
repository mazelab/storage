<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_ValueObject_Storage
    extends Core_Model_ValueObject
{

    /**
     * log action for conflicted storage
     */
    CONST LOG_ACTION_STORAGE_DIFF = 'storage differences';
    
    /**
     * log action for a resolved storage conflict
     */
    CONST LOG_ACTION_STORAGE_DIFF_RESOLVED = 'resolved storage differences';
    
    /**
     * message when storage is conflicted
     */
    CONST MESSAGE_STORAGE_DIFF = 'Differences in storage %1$s';
    
    /**
     * message when storage conflict was resolved
     */
    CONST MESSAGE_STORAGE_DIFF_RESOLVED = 'Resolved differences of storage %1$s';
    
    /**
     * flag to build search index
     * 
     * @var boolean
     */
    protected $_rebuildSearchIndex;
    
    /**
     * sets conflict entry in log
     */
    protected function _addConflictToLog()
    {
        $this->_getLogger()->setType(Core_Model_Logger::TYPE_CONFLICT)
                ->setMessage(self::MESSAGE_STORAGE_DIFF)
                ->setMessageVars($this->getName())->setData($this->getConflicts())
                ->setAction(self::LOG_ACTION_STORAGE_DIFF)
                ->setModuleRef(MazelabStorage_Model_StorageManager::MODULE_NAME)
                ->setRoute(array($this->getId()), 'mazelab-storage_edit')
                ->setNodeRef($this->getData('nodeId'))
                ->saveByContext($this->getName());
    }
    
    /**
     * get provider instance
     * 
     * @return MazelabStorage_Model_Dataprovider_Interface_Storage
     */
    protected function _getProvider()
    {
        return MazelabStorage_Model_Dataprovider_DiFactory::getStorage();
    }
    
    /**
     * loads context from data backend with a provider
     * returns loaded context as array
     * 
     * @return array
     */
    protected function _load()
    {
        return $this->_getProvider()->getStorage($this->getId());
    }
    
    /**
     * saves context into data backend with a provider
     * if successful it returns the id of the data set which was saved
     *
     * @param array $unmappedData
     *
     * @return string $id data backend identification
     */
    protected function _save(array $unmappedData) {
        return $this->_getProvider()->setStorage($unmappedData, $this->getId());
    }
    
    /**
     * apply current configuration
     * 
     * @param boolean $save (default true) save or only set commands
     * @return boolean
     */
    public function apply($save = true)
    {
        return MazelabStorage_Model_DiFactory::getApplyStorage()->apply($this, $save);
    }
    
    /**
     * disables this storage
     * 
     * @return boolean
     */
    public function disable()
    {
        if($this->getStatus() === false) {
            return true;
        }

        if(!$this->setData(array('status' => false))->save()) {
            return false;
        }
        
        $this->apply();
        
        return true;
    }
    
    /**
     * enalbes this storage
     * 
     * @return boolean
     */
    public function enable()
    {
        if($this->getStatus() === true) {
            return true;
        }

        if(!$this->setData(array('status' => true))->save()) {
            return false;
        }
        
        $this->apply();
        
        return true;
    }
    
    /**
     * evaluates given report
     * 
     * @param array $data report data
     * @return boolean
     */
    public function evalReport(array $data)
    {
        if($this->getData('delete')) {
            if(!$data) {
                return MazelabStorage_Model_DiFactory::getStorageManager()->deleteStorage($this->getId());
            }
            return $this->apply(false);
        }
        
        $oldDiffs = $this->getConflicts();
        $this->setRemoteData($data);

        if($this->getConflicts(MazeLib_Bean::STATUS_MANUALLY)) {
            $this->_addConflictToLog();
        } elseif ($this->getConflicts()) {
            $this->apply(false);
        } elseif($oldDiffs) {
            $this->resolveDiff();
        }
        
        return $this->setFlags()->save();
    }
    
    /**
     * returns the Bean with the loaded data from data backend
     * 
     * @param boolean $new force new bean struct
     * @return MazelabStorage_Model_Bean_Storage
    */
    public function getBean($new = false)
    {
        if($new || !$this->_valueBean || !$this->_valueBean instanceof MazelabStorage_Model_Bean_Storage) {
            $this->_valueBean = new MazelabStorage_Model_Bean_Storage();
        }
        
        $this->load();
        
        return $this->_valueBean;
    }
    
    /**
     * get actual client
     * 
     * @return Core_Model_ValueObject_Client|null
     */
    public function getClient()
    {
        if(!($clientId = $this->getData('clientId'))) {
            return null;
        }
        
        return Core_Model_DiFactory::getClientManager()->getClient($clientId);
    }
    
    /**
     * alias for getData('name')
     * 
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }
    
    /**
     * get actual node
     * 
     * @return Core_Model_ValueObject_Node|null
     */
    public function getNode()
    {
        if(!($nodeId = $this->getData('nodeId'))) {
            return null;
        }
        
        return Core_Model_DiFactory::getNodeManager()->getNode($nodeId);
    }
    
    /**
     * get status value
     * 
     * alias for ->getData('status')
     * 
     * @return boolean
     */
    public function getStatus()
    {
        return $this->getData('status');
    }
    
    /**
     * removes commands from current node
     * 
     * @return boolean
     */
    public function removeCommands()
    {
        return MazelabStorage_Model_DiFactory::getApplyStorage()->remove($this);
    }
    
    /**
     * if corresponding log entry exists it will be changed into resolved state
     * 
     * @return boolean
     */
    public function resolveDiff()
    {
        if(!Core_Model_DiFactory::getLogManager()->getContextLog($this->getName(),
                Core_Model_Logger::TYPE_CONFLICT, self::LOG_ACTION_STORAGE_DIFF)) {
            return true;
        }

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_DIFF_RESOLVED)
                ->setMessageVars($this->getName())
                ->setType(Core_Model_Logger::TYPE_NOTIFICATION)
                ->setAction(self::LOG_ACTION_STORAGE_DIFF_RESOLVED);
        
        $this->_getLogger()->saveByContext($this->getName(),
                Core_Model_Logger::TYPE_CONFLICT, self::LOG_ACTION_STORAGE_DIFF);
        
        return true;
    }
    
    /**
     * sets/adds new data set as local data
     * 
     * @param array $data
     * @param boolean $encryptPassword
     * 
     * @return MazelabStorage_Model_ValueObject_Storage
     */
    public function setData(array $data, $encryptPassword = true)
    {
        if(array_key_exists('status', $data)) {
            $data["status"] = (boolean) $data["status"];
        }
        if(array_key_exists('nodeId', $data) &&
                ($node = Core_Model_DiFactory::getNodeManager()->getNode($data['nodeId']))) {
            $data['nodeName'] = $node->getName();
        }
        if(array_key_exists('clientId', $data) &&
                ($client = Core_Model_DiFactory::getClientManager()->getClient($data['clientId']))) {
            $data['clientLabel'] = $client->getLabel();
        }
        if(array_key_exists('name', $data)) {
            $this->_rebuildSearchIndex = true;
        }
        if($encryptPassword && array_key_exists('password', $data)) {
            $data['password'] = crypt($data['password']);
        }
        
        return parent::setData($data);
    }
    
    /**
     * flag this storage to be deleted after node sync
     * 
     * @return boolean
     */
    public function setDeleteFlag()
    {
        $imported = $this->getData('imported');
        if(!$this->setData(array('delete' => true))->unsetProperty('imported')->save()) {
            return false;
        }

        if($imported) {
            MazelabStorage_Model_DiFactory::getReportManager()->writeCheckedImportLog();
        }

        $this->apply();

        return true;
    }
    
    /**
     * sets or unsets pending flag
     * 
     * @return MazelabStorage_Model_ValueObject_Storage
     */
    public function setFlags()
    {
        if($this->getData('nodeId') && $this->getConflicts(MazeLib_Bean::STATUS_MANUALLY)) {
            $this->unsetProperty('pending')->setProperty('conflicted', true);
        } elseif($this->getData('nodeId') && $this->getConflicts()) {
            $this->unsetProperty('conflicted')->setProperty('pending', true);
        } else {
            $this->unsetProperty('pending')->unsetProperty('conflicted');
        }
        
        return $this;
    }
    
    /**
     * sets/adds new data set as remote data
     * 
     * @param array $data
     * @return MazelabStorage_Model_ValueObject_Storage
     */
    public function setRemoteData($data)
    {
        if(array_key_exists('status', $data) && ($data['status'] === 'enabled' || $data['status'] === true)) {
            $data['status'] = true;
        } elseif(array_key_exists('status', $data)) {
            $data['status'] = false;
        }

        return parent::setRemoteData($data);
    }

}